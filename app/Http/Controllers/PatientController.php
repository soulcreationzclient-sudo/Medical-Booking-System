<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function search(Request $request)
    {
        $hospitalId = auth()->user()->hospital_id;
        $name       = trim($request->get('name', ''));
        $phone      = trim($request->get('phone', ''));
        $icPassport = trim($request->get('ic_passport', ''));

        // Scoped to this hospital via whereIn subquery — no JOIN/GROUP BY
        // avoids MySQL ONLY_FULL_GROUP_BY strict mode error
        $hospitalPhones = DB::table('bookings')
            ->where('hospital_id', $hospitalId)
            ->select('patient_phone');

        $patients = DB::table('patients')
            ->whereIn('phone_no', $hospitalPhones)
            ->when($name,       fn($q) => $q->where('name',          'like', "%{$name}%"))
            ->when($phone,      fn($q) => $q->where('phone_no',      'like', "%{$phone}%"))
            ->when($icPassport, fn($q) => $q->where('ic_passport_no','like', "%{$icPassport}%"))
            ->selectRaw('patients.*, (
                SELECT COUNT(*)
                FROM bookings
                WHERE bookings.patient_phone = patients.phone_no
                AND   bookings.hospital_id   = ?
            ) as bookings_count', [$hospitalId])
            ->orderBy('name')
            ->get();

        return view('hospital_admin.patient_search', compact('patients'));
    }

    public function show($id)
    {
        $hospitalId = auth()->user()->hospital_id;
        $patient    = DB::table('patients')->where('id', $id)->first();

        if (!$patient) abort(404);

        // Only bookings for this hospital
        $bookings = DB::table('bookings')
            ->leftJoin('doctors', 'doctors.id', '=', 'bookings.doctor_id')
            ->select('bookings.*', 'doctors.name as doctor_name')
            ->where('bookings.patient_phone', $patient->phone_no)
            ->where('bookings.hospital_id', $hospitalId)
            ->orderByDesc('bookings.booking_date')
            ->orderByDesc('bookings.start_time')
            ->get();

        $bookingIds = $bookings->pluck('id');

        // Only prescriptions for this hospital
        $prescriptions = $bookingIds->isEmpty()
            ? collect()
            : DB::table('prescriptions')
                ->whereIn('booking_id', $bookingIds)
                ->where('hospital_id', $hospitalId)
                ->orderByDesc('created_at')
                ->get();

        // Only medicines for this hospital
        $medicines = DB::table('medicines')
            ->where('hospital_id', $hospitalId)
            ->get();

        return view('hospital_admin.patient_show',
            compact('patient', 'bookings', 'prescriptions', 'medicines'));
    }

    public function update(Request $request, $id)
    {
        $patient = DB::table('patients')->where('id', $id)->first();
        if (!$patient) abort(404);

        $request->validate([
            'name'                   => 'required|string|max:255',
            'phone_no'               => 'required|string|max:20',
            'ic_passport_no'         => 'nullable|string|max:50',
            'dob'                    => 'nullable|date',
            'age'                    => 'nullable|integer',
            'gender'                 => 'nullable|in:male,female,other',
            'blood_type'             => 'nullable|string|max:5',
            'marital_status'         => 'nullable|string|max:20',
            'nationality'            => 'nullable|string|max:50',
            'address'                => 'nullable|string|max:500',
            'city'                   => 'nullable|string|max:100',
            'state'                  => 'nullable|string|max:100',
            'postcode'               => 'nullable|string|max:20',
            'country'                => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_no'   => 'nullable|string|max:20',
        ]);

        DB::table('patients')->where('id', $id)->update([
            'name'                   => $request->name,
            'phone_no'               => $request->phone_no,
            'ic_passport_no'         => $request->ic_passport_no,
            'dob'                    => $request->dob,
            'age'                    => $request->age,
            'gender'                 => $request->gender,
            'blood_type'             => $request->blood_type,
            'marital_status'         => $request->marital_status,
            'nationality'            => $request->nationality,
            'address'                => $request->address,
            'city'                   => $request->city,
            'state'                  => $request->state,
            'postcode'               => $request->postcode,
            'country'                => $request->country,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_no'   => $request->emergency_contact_no,
            'updated_at'             => now(),
        ]);

        return redirect()->route('hospital_admin.patients.profile', $id)
                         ->with('success', 'Patient updated successfully.');
    }

    public function addPrescription(Request $request, $id)
    {
        $request->validate([
            'medicine_id'  => 'required|integer|exists:medicines,id',
            'dosage'       => 'required|string|max:100',
            'frequency'    => 'required|string|max:100',
            'duration'     => 'nullable|string|max:100',
            'instructions' => 'nullable|string|max:1000',
            'booking_id'   => 'nullable|integer|exists:bookings,id',
        ]);

        $hospitalId = auth()->user()->hospital_id;

        // Verify medicine belongs to this hospital
        $medicine = DB::table('medicines')
            ->where('id', $request->medicine_id)
            ->where('hospital_id', $hospitalId)
            ->first();

        if (!$medicine) {
            return back()->with('error', 'Medicine not found.');
        }

        // Use submitted booking_id or auto-assign latest booking for THIS hospital
        $bookingId = $request->booking_id;
        if (!$bookingId) {
            $patient   = DB::table('patients')->where('id', $id)->first();
            $bookingId = DB::table('bookings')
                ->where('patient_phone', $patient->phone_no)
                ->where('hospital_id', $hospitalId)
                ->orderByDesc('booking_date')
                ->value('id');
        }

        DB::table('prescriptions')->insert([
            'booking_id'    => $bookingId,
            'hospital_id'   => $hospitalId,
            'medicine_name' => $medicine->name,
            'dosage'        => $request->dosage,
            'frequency'     => $request->frequency,
            'duration'      => $request->duration,
            'instructions'  => $request->instructions,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->route('hospital_admin.patients.profile', $id)
                         ->with('success', 'Prescription added successfully.');
    }

    public function deletePrescription($id, $pid)
    {
        $hospitalId = auth()->user()->hospital_id;

        // Only delete prescriptions belonging to this hospital
        DB::table('prescriptions')
            ->where('id', $pid)
            ->where('hospital_id', $hospitalId)
            ->delete();

        return redirect()->route('hospital_admin.patients.profile', $id)
                         ->with('success', 'Prescription deleted.');
    }
}