<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    // ─────────────────────────────────────────────
    // GET /hospital-admin/patients/search
    // ─────────────────────────────────────────────
    public function search(Request $request)
    {
        $patients = collect();

        $name       = trim($request->get('name', ''));
        $phone      = trim($request->get('phone', ''));
        $icPassport = trim($request->get('ic_passport', ''));

        // Only query if at least one field is filled
        if ($name || $phone || $icPassport) {
            $patients = DB::table('patients')
                ->when($name, function ($q) use ($name) {
                    $q->where('name', 'like', "%{$name}%");
                })
                ->when($phone, function ($q) use ($phone) {
                    $q->where('phone_no', 'like', "%{$phone}%");
                })
                ->when($icPassport, function ($q) use ($icPassport) {
                    $q->where('ic_passport_no', 'like', "%{$icPassport}%");
                })
                // Attach booking count
                ->selectRaw('patients.*, (
                    SELECT COUNT(*) FROM bookings
                    WHERE bookings.patient_phone = patients.phone_no
                ) as bookings_count')
                ->orderBy('name')
                ->get();
        }

        return view('hospital_admin.patient_search', compact('patients'));
    }

    // ─────────────────────────────────────────────
    // GET /hospital-admin/patients/{id}
    // ─────────────────────────────────────────────
    public function show($id)
    {
        $patient = DB::table('patients')->where('id', $id)->first();

        if (!$patient) {
            abort(404);
        }

        // Booking history with doctor name
        $bookings = DB::table('bookings')
            ->leftJoin('doctors', 'doctors.id', '=', 'bookings.doctor_id')
            ->select(
                'bookings.*',
                'doctors.name as doctor_name'
            )
            ->where('bookings.patient_phone', $patient->phone_no)
            ->orderByDesc('bookings.booking_date')
            ->orderByDesc('bookings.start_time')
            ->get();

        return view('hospital_admin.patient_show', compact('patient', 'bookings'));
    }
}