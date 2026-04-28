<?php

use App\Http\Controllers\Bookingcontroller;
use App\Http\Controllers\Caseentrycontroller;
use App\Http\Controllers\Doctorcontroller;
use App\Http\Controllers\Hospitaladmincontroller;
use App\Http\Controllers\Superadmincontroller;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\MedicineController;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Auth::routes();

Route::get('/', fn() => redirect()->route('login'));

// ══════════════════════════════════════════════════════════
//  SHARED
// ══════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:super_admin,hospital_admin,doctor'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

// ══════════════════════════════════════════════════════════
//  SUPER ADMIN
// ══════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super_admin')
    ->name('super_admin.')
    ->group(function () {
        Route::controller(Superadmincontroller::class)->group(function () {
            Route::get('/dashboard',                'index')->name('dashboard');
            Route::get('/hospitals_add',            'add_hospital_view')->name('hospitals_add_view');
            Route::get('/hospitals_add_form',       'add_hospital_form')->name('hospitals_add_form');
            Route::post('/hospitals_add',           'add_hospital')->name('hospital_add');
            Route::get('/hospitals_edit_view/{id}', 'edit_hospital_view')->name('hospitals_edit_view');
            Route::put('/hospital_edit/{id}',       'edit_hospital')->name('hospital_edit');
            Route::post('/hospital_delete',         'delete_hospital')->name('hospital_delete');
            Route::get('/delete_s3',                'delete_s3');
        });
    });

// ══════════════════════════════════════════════════════════
//  HOSPITAL ADMIN
// ══════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:hospital_admin'])
    ->prefix('hospital_admin')
    ->name('hospital_admin.')
    ->group(function () {

        Route::controller(Hospitaladmincontroller::class)->group(function () {

            // ── Dashboard ───────────────────────────────
            Route::get('/dashboard', 'index')->name('dashboard');

            // ── Doctors ─────────────────────────────────
            Route::get('/doctors_add',          'add_doctor_view')->name('doctors_add_view');
            Route::get('/doctor_form',          'doctor_form')->name('doctors_form');
            Route::post('/add_doctor',          'doctor_add')->name('doctor_add');
            Route::post('/delete_doctor',       'doctor_delete')->name('doctor_delete');
            Route::get('/edit_doctor/{id}',     'edit_doctor_view')->name('doctors_edit_view');
            Route::post('/update_doctor/{id}',  'doctor_add')->name('doctors_update');

            // ── Specialization ──────────────────────────
            Route::get('/specialization',         'specialization_view')->name('specialization');
            Route::post('/specialization_add',    'specialization_add')->name('specialization_add');
            Route::post('/specialization_delete', 'specialization_delete')->name('specialization_delete');
            Route::post('/specialization_edit',   'specialization_edit')->name('specialization_edit');

            // ── In-person & Bookings ────────────────────
            Route::get('/in_person_form',               'in_person_form')->name('inpersonform');
            Route::post('/bookings/ajax_store',         'ajax_store')->name('inperson');
            Route::get('/overall_bookings',             'overall_bookings')->name('overall_bookings');
            Route::get('/calendar', 'calendar')->name('calendar');
            Route::post('/bookings/{id}/reschedule',    'reschedule')->name('bookings.reschedule');
            Route::post('/bookings/{id}/update-status', 'update_status')->name('bookings.updatestatus');
            Route::post('/bookings/{id}/assign',        'assignDoctor');

            // ── Medicines ───────────────────────────────
            Route::get('/medicines',         'medicines_index')->name('medicines.index');
            Route::post('/medicines',        'medicine_store')->name('medicines.store');
            Route::put('/medicines/{id}',    'medicine_update')->name('medicines.update');
            Route::delete('/medicines/{id}', 'medicine_delete')->name('medicines.delete');

            // ── Treatments ──────────────────────────────
            Route::get('/treatments',           'treatments_index')->name('treatments.index');
            Route::post('/treatments',          'treatment_store')->name('treatments.store');
            Route::put('/treatments/{id}',      'treatment_update')->name('treatments.update');
            Route::delete('/treatments/{id}',   'treatment_delete')->name('treatments.delete');
            Route::get('/treatment-price/{id}', 'getTreatmentPrice')->name('treatments.price');

            // ── Prescriptions ───────────────────────────
            // ⚠️ static segment first, wildcard second
            Route::get('/prescriptions/create/{bookingId}', 'prescription_create')->name('prescriptions.create');
            Route::post('/prescriptions/{bookingId}',       'prescription_store')->name('prescriptions.store');

            // ── Financials ──────────────────────────────
            Route::get('/financials',          'financials_index')->name('financials.index');
            Route::post('/financials',         'financial_store')->name('financials.store');
            Route::delete('/financials/{id}',  'financial_delete')->name('financials.delete');

            // ── Speedbots Settings ──────────────────────
            Route::get('/speedbots-settings',         'speedbots_settings')->name('speedbots.settings');
            Route::post('/speedbots-settings/update', 'speedbots_settings_update')->name('speedbots.settings.update');

            // ── Patients ────────────────────────────────
            // ⚠️ CRITICAL ORDER — static before wildcard:
            // 1. List
            Route::get('/patients', 'patients_index')->name('patients.index');

            // 2. Search — must come BEFORE /patients/{id}
            Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');

            // 3. Billing sub-routes (safe — extra path segments)
            Route::post('/patients/{id}/billing',               'patient_add_billing')->name('patients.billing.store');
            Route::post('/patients/{id}/billing/{entryId}/pay', 'patient_mark_paid')->name('patients.billing.pay');
            Route::delete('/patients/{id}/billing/{entryId}',   'patient_billing_delete')->name('patients.billing.delete');

            // 4. PatientController sub-routes (PUT/POST/DELETE — no GET conflict)
            Route::put('/patients/{id}/update',                 [PatientController::class, 'update'])->name('patients.update');
            Route::post('/patients/{id}/prescriptions',         [PatientController::class, 'addPrescription'])->name('patients.prescriptions.add');
            Route::delete('/patients/{id}/prescriptions/{pid}', [PatientController::class, 'deletePrescription'])->name('patients.prescriptions.delete');

            // 5. Profile wildcard — MUST be last GET /patients/{anything}
            Route::get('/patients/{id}', 'patient_profile')->name('patients.profile');
        });

        // ── MedicineController ──────────────────────────
        Route::get('/medicine-price/{id}', [MedicineController::class, 'getPrice'])->name('medicine.price');
    });

// ══════════════════════════════════════════════════════════
//  PUBLIC / BOOKING ROUTES
// ══════════════════════════════════════════════════════════

Route::get('/hospital_booking/{hospital_code}', [Hospitaladmincontroller::class, 'hospital_show']);
Route::get('/doctor_booking/{id}',              [Bookingcontroller::class, 'booking'])->name('patient.booking');
Route::post('/booking/{doctor}/ajax',           [Bookingcontroller::class, 'ajaxStore'])->name('booking.ajax.store');
Route::get('/booking/status/{code}',            [Bookingcontroller::class, 'status'])->name('booking.status');
Route::get('/booking/verify/{token}',           [Bookingcontroller::class, 'verify'])->name('booking.verify');
Route::get('/booking/lookup-patient',           [Bookingcontroller::class, 'lookupPatient'])->name('booking.lookup.patient');
Route::post('/booking/{doctorId}',              [Bookingcontroller::class, 'ajaxStore'])->name('booking.store');
Route::view('/book', 'boooking_form');

// ══════════════════════════════════════════════════════════
//  DOCTOR ROUTES
// ══════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:doctor'])
    ->prefix('doctor')
    ->name('doctor.')
    ->group(function () {

        Route::controller(Doctorcontroller::class)->group(function () {
            Route::get('/dashboard',                     'index')->name('dashboard');
            Route::get('/schedule',                      'schedule')->name('schedule');
            Route::post('/doctor/schedule/save',         'schedule_save')->name('schedule.save');
            Route::get('/overall_bookings',              'overall_bookings')->name('overall_bookings');
            Route::get('/calendar', 'calendar')->name('calendar');
            Route::post('/bookings/{id}/update-status',  'update_status')->name('bookings.updatestatus');
            Route::post('/add_slot',                     'add_slot')->name('add_slot');
            Route::post('/bookings/{id}/reschedule',     'reschedule')->name('bookings.reschedule');
        });

        Route::controller(Caseentrycontroller::class)->group(function () {
            Route::get('/bookings/{booking}/case-entry',  'create')->name('case_entry.create');
            Route::post('/bookings/{booking}/case-entry', 'store')->name('case_entry.store');
            Route::get('/case-entry/{caseEntry}',         'show')->name('case_entry.show');
        });
    });

// ══════════════════════════════════════════════════════════
//  PRESCRIPTION ROUTES (dedicated PrescriptionController)
// ══════════════════════════════════════════════════════════

// ⚠️ Static routes before wildcard routes
Route::get('/prescriptions/patient/{phone}',    [PrescriptionController::class, 'patientHistory'])->name('prescriptions.patient.history');
Route::get('/prescriptions/{bookingId}/print',  [PrescriptionController::class, 'print'])->name('prescriptions.print');
Route::get('/prescriptions/{bookingId}',        [PrescriptionController::class, 'show'])->name('prescriptions.show');
Route::post('/prescriptions/{bookingId}/store', [PrescriptionController::class, 'store'])->name('prescriptions.store.dedicated');
Route::put('/prescriptions/{id}',               [PrescriptionController::class, 'update'])->name('prescriptions.update');
Route::delete('/prescriptions/{id}',            [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');

// ══════════════════════════════════════════════════════════
//  MISC
// ══════════════════════════════════════════════════════════

Route::post('/test', [Superadmincontroller::class, 'test'])->name('test');

//search sytem routes
use App\Http\Controllers\AiSearchController;

Route::prefix('hospital-admin')->name('hospital_admin.')->group(function () {
    Route::get('/ai-search', [AiSearchController::class, 'index'])->name('ai_search.index');
    Route::post('/ai-search', [AiSearchController::class, 'search'])->name('ai_search.search');
});