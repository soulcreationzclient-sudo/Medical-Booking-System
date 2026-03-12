<?php

use App\Http\Controllers\Bookingcontroller;
use App\Http\Controllers\Caseentrycontroller;
use App\Http\Controllers\Doctorcontroller;
use App\Http\Controllers\Hospitaladmincontroller;
use App\Http\Controllers\Superadmincontroller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PatientController;

Auth::routes();

Route::middleware(['auth', 'role:super_admin,hospital_admin,doctor'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});
Route::middleware(['auth', 'role:super_admin'])->prefix('super_admin')->name('super_admin.')->group(function () {
    Route::controller(Superadmincontroller::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/hospitals_add', 'add_hospital_view')->name('hospitals_add_view');
        Route::get('/hospitals_add_form', 'add_hospital_form')->name('hospitals_add_form');
        Route::post('/hospitals_add', 'add_hospital')->name('hospital_add');
        Route::get('/hospitals_edit_view/{id}', 'edit_hospital_view')->name('hospitals_edit_view');
        Route::put('/hospital_edit/{id}', 'edit_hospital')->name('hospital_edit');
        Route::post('/hospital_delete', 'delete_hospital')->name('hospital_delete');
        Route::get('/delete_s3', 'delete_s3');
    });
});
Route::middleware(['auth', 'role:hospital_admin'])->prefix('hospital_admin')->name('hospital_admin.')->group(function () {
    Route::controller(Hospitaladmincontroller::class)->group(function () {
        Route::post('/bookings/{id}/reschedule', 'reschedule')->name('bookings.reschedule');
        Route::post('/bookings/{id}/update-status','update_status')->name('bookings.updatestatus');
        Route::post('/bookings/ajax_store','ajax_store')->name('inperson');
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/doctors_add', 'add_doctor_view')->name('doctors_add_view');
        Route::get('/doctor_form', 'doctor_form')->name('doctors_form');
        Route::post('/add_doctor', 'doctor_add')->name('doctor_add');
        Route::post('/delete_doctor', 'doctor_delete')->name('doctor_delete');
        Route::get('/edit_doctor/{id}', 'edit_doctor_view')->name('doctors_edit_view');
        Route::post('/update_doctor/{id}', 'doctor_add')->name('doctors_update');
        Route::get('/specialization', 'specialization_view')->name('specialization');
        Route::post('/specialization_add', 'specialization_add')->name('specialization_add');
        Route::post('/specialization_delete', 'specialization_delete')->name('specialization_delete');
        Route::post('/specialization_edit', 'specialization_edit')->name('specialization_edit');
        Route::get('/in_person_form','in_person_form')->name('inpersonform');
        Route::get('/overall_bookings','overall_bookings')->name('overall_bookings');
        Route::post('/bookings/{id}/assign','assignDoctor');
    });
});
Route::get('/hospital_booking/{hospital_code}', [Hospitaladmincontroller::class, 'hospital_show']);
Route::get('/doctor_booking/{id}', [Bookingcontroller::class, 'booking'])->name('patient.booking');
Route::post('/booking/{doctor}/ajax', [BookingController::class, 'ajaxStore'])
    ->name('booking.ajax.store');
Route::get('/booking/status/{code}', [BookingController::class, 'status'])
    ->name('booking.status');
Route::get('/booking/verify/{token}', [BookingController::class, 'verify'])
    ->name('booking.verify');

Route::middleware(['auth', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::controller(Doctorcontroller::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/schedule', 'schedule')->name('schedule');
        Route::post('/doctor/schedule/save', 'schedule_save')->name('schedule.save');
        Route::get('/overall_bookings','overall_bookings')->name('overall_bookings');
        Route::post('/bookings/{id}/update-status','update_status')->name('bookings.updatestatus');
        Route::post('/add_slot','add_slot')->name('add_slot');

            // Reschedule booking
        Route::post('/bookings/{id}/reschedule', [Doctorcontroller::class, 'reschedule'])
        ->name('bookings.reschedule');
    });
});
Route::middleware(['auth','role:doctor'])->prefix('doctor')->name('doctor.')->group(function(){
    Route::controller(Caseentrycontroller::class)->group(function(){
            // Case Entry Routes
    Route::get('/bookings/{booking}/case-entry',  'create')->name('doctor.case_entry.create');
    Route::post('/bookings/{booking}/case-entry', 'store')->name('doctor.case_entry.store');
    Route::get('/case-entry/{caseEntry}','show')->name('case_entry.show');
    });
});
Route::post('/test', [Superadmincontroller::class, 'test'])->name('test');
Route::view('/book', 'boooking_form');

// ── Add these lines to your routes/web.php ──

// Patient pre-fill lookup (used by both forms via JS)
Route::get('/booking/lookup-patient', [BookingController::class, 'lookupPatient'])
    ->name('booking.lookup.patient');

// Public booking store (already exists — ensure it calls ajaxStore)
Route::post('/booking/{doctorId}', [BookingController::class, 'ajaxStore'])
    ->name('booking.ajax.store');

// Admin in-person booking store (already exists)
Route::post('/hospital-admin/inperson', [BookingController::class, 'inPersonStore'])
    ->name('hospital_admin.inperson')
    ->middleware(['auth', 'hospital_admin']);

// ── Add these inside your hospital_admin middleware/auth group in routes/web.php ──

Route::get('/hospital-admin/patients/search', [PatientController::class, 'search'])
    ->name('hospital_admin.patients.search');

Route::get('/hospital-admin/patients/{id}', [PatientController::class, 'show'])
    ->name('hospital_admin.patients.show');






Route::get('/prescriptions/{bookingId}',         [PrescriptionController::class, 'show'])   ->name('prescriptions.show');
Route::post('/prescriptions/{bookingId}',         [PrescriptionController::class, 'store'])  ->name('prescriptions.store');
Route::put('/prescriptions/{id}',                 [PrescriptionController::class, 'update']) ->name('prescriptions.update');
Route::delete('/prescriptions/{id}',              [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');
Route::get('/prescriptions/{bookingId}/print',    [PrescriptionController::class, 'print'])  ->name('prescriptions.print');
Route::get('/prescriptions/patient/{phone}',      [PrescriptionController::class, 'patientHistory'])->name('prescriptions.patient.history');


// ── 2. In your overall_bookings blade, add a "Prescriptions" button per booking card ──
// Find where the Accept/Reject/Reschedule buttons are and add this alongside them:

/*
<a href="{{ route('prescriptions.show', $booking->id) }}"
   class="btn btn-sm"
   style="background:#eff6ff;color:#1363C6;border:1px solid #bfdbfe;font-weight:600;border-radius:8px;padding:6px 14px">
    💊 Prescriptions
</a>
*/


// ── 3. In patient_show.blade.php, add a "View Prescription History" button ──
// Find the booking history section header and add:

/*
<a href="{{ route('prescriptions.patient.history', $patient->phone_no) }}"
   style="background:#eff6ff;color:#1363C6;padding:5px 14px;border-radius:8px;
          font-size:13px;font-weight:600;text-decoration:none;">
    💊 Prescription History
</a>
*/