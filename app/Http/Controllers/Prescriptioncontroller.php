<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Medicine;

class PrescriptionController extends Controller
{
    // ─────────────────────────────────────────────
    // GET: Show prescription form for a booking
    // /prescriptions/{bookingId}
    // ─────────────────────────────────────────────
    public function show($bookingId)
    {
        $booking = DB::table('bookings')
            ->leftJoin('doctors', 'doctors.id', '=', 'bookings.doctor_id')
            ->leftJoin('hospitals', 'hospitals.id', '=', 'bookings.hospital_id')
            ->select(
                'bookings.*',
                'doctors.name as doctor_name',
                'hospitals.hospital_name'
            )
            ->where('bookings.id', $bookingId)
            ->firstOrFail();

        // Get patient details (for allergy flag)
        $patient = DB::table('patients')
            ->where('phone_no', $booking->patient_phone)
            ->first();

        // Existing prescriptions for this booking
        $prescriptions = DB::table('prescriptions')
            ->where('booking_id', $bookingId)
            ->orderBy('id')
            ->get();
        
        $medicines = DB::table('medicines')
            ->where('hospital_id', auth()->user()->hospital_id)
            ->get();

        return view('prescriptions.show', compact('booking', 'patient', 'prescriptions', 'medicines'));
    }

    // ─────────────────────────────────────────────
    // POST: Add a new prescription line
    // ─────────────────────────────────────────────
    public function store(Request $request, $bookingId)
    {
        $request->validate([
            'medicine_name' => 'required|string|max:255',
            'dosage'        => 'nullable|string|max:100',
            'frequency'     => 'nullable|string|max:100',
            'duration'      => 'nullable|string|max:100',
            'instructions'  => 'nullable|string',
        ]);

        // Check booking exists and get patient_id via case_entry
        $booking = DB::table('bookings')->where('id', $bookingId)->firstOrFail();

        // Get or resolve case_entry_id (nullable — prescription can exist without case entry)
        $caseEntry = DB::table('case_entries')->where('booking_id', $bookingId)->first();

        DB::table('prescriptions')->insert([
            'booking_id'     => $bookingId,
            'case_entry_id'  => $caseEntry?->id ?? null,
            'medicine_name'  => $request->medicine_name,
            'dosage'         => $request->dosage,
            'frequency'      => $request->frequency,
            'duration'       => $request->duration,
            'instructions'   => $request->instructions,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect()
            ->route('prescriptions.show', $bookingId)
            ->with('success', 'Prescription added successfully.');
    }

    // ─────────────────────────────────────────────
    // POST: Update a prescription line
    // ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $request->validate([
            'medicine_name' => 'required|string|max:255',
            'dosage'        => 'nullable|string|max:100',
            'frequency'     => 'nullable|string|max:100',
            'duration'      => 'nullable|string|max:100',
            'instructions'  => 'nullable|string',
        ]);

        $prescription = DB::table('prescriptions')->where('id', $id)->firstOrFail();

        DB::table('prescriptions')->where('id', $id)->update([
            'medicine_name' => $request->medicine_name,
            'dosage'        => $request->dosage,
            'frequency'     => $request->frequency,
            'duration'      => $request->duration,
            'instructions'  => $request->instructions,
            'updated_at'    => now(),
        ]);

        return redirect()
            ->route('prescriptions.show', $prescription->booking_id)
            ->with('success', 'Prescription updated.');
    }

    // ─────────────────────────────────────────────
    // DELETE: Remove a prescription line
    // ─────────────────────────────────────────────
    public function destroy($id)
    {
        $prescription = DB::table('prescriptions')->where('id', $id)->firstOrFail();
        $bookingId    = $prescription->booking_id;

        DB::table('prescriptions')->where('id', $id)->delete();

        return redirect()
            ->route('prescriptions.show', $bookingId)
            ->with('success', 'Prescription removed.');
    }

    // ─────────────────────────────────────────────
    // GET: Printable PDF view for a booking
    // ─────────────────────────────────────────────
    public function print($bookingId)
    {
        $booking = DB::table('bookings')
            ->leftJoin('doctors',   'doctors.id',   '=', 'bookings.doctor_id')
            ->leftJoin('hospitals', 'hospitals.id', '=', 'bookings.hospital_id')
            ->select('bookings.*', 'doctors.name as doctor_name', 'hospitals.hospital_name')
            ->where('bookings.id', $bookingId)
            ->firstOrFail();

        $patient = DB::table('patients')
            ->where('phone_no', $booking->patient_phone)
            ->first();

        $prescriptions = DB::table('prescriptions')
            ->where('booking_id', $bookingId)
            ->orderBy('id')
            ->get();

        return view('prescriptions.print', compact('booking', 'patient', 'prescriptions'));
    }

    // ─────────────────────────────────────────────
    // GET: Full prescription history for a patient
    // /prescriptions/patient/{phone}
    // ─────────────────────────────────────────────
    public function patientHistory($phone)
    {
        $patient = DB::table('patients')->where('phone_no', $phone)->first();

        if (!$patient) {
            abort(404);
        }

        $prescriptions = DB::table('prescriptions')
            ->join('bookings', 'bookings.id', '=', 'prescriptions.booking_id')
            ->leftJoin('doctors', 'doctors.id', '=', 'bookings.doctor_id')
            ->select(
                'prescriptions.*',
                'bookings.booking_date',
                'bookings.start_time',
                'bookings.cause',
                'doctors.name as doctor_name'
            )
            ->where('bookings.patient_phone', $phone)
            ->orderByDesc('bookings.booking_date')
            ->get();

        return view('prescriptions.history', compact('patient', 'prescriptions'));
    }
}