<?php

namespace App\Http\Controllers;

use App\Mail\BookingVerificationMail;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Bookingcontroller extends Controller
{
    public function booking(Request $request, $doctorId)
    {
        $date     = $request->get('date');
        $phone_no = $request->get('phone_no') ?? null;
        $doctor   = Doctor::findOrFail($doctorId);
        $day      = \Carbon\Carbon::parse($date)->format('l');

        $schedule = DB::table('schedules')
            ->where('doctor_id', $doctorId)
            ->where('day', $day)
            ->first();

        if (!$schedule) {
            return back()->with('error', 'Doctor not available on this day');
        }

        if ($schedule->is_off == 1) {
            return back()->with('error', 'Doctor not available on this day');
        }

        $bookings = DB::table('bookings')
            ->where('doctor_id', $doctorId)
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'accepted'])
            ->get();

        $slots = $this->generateAvailableSlots(
            $schedule->start_time,
            $schedule->end_time,
            $bookings,
            $doctor->slot ?? 10
        );

        return view('users.booking_form', compact(
            'doctorId',
            'date',
            'slots',
            'phone_no'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // AJAX: Look up patient by phone number for pre-filling
    // GET /booking/lookup-patient?phone=...
    // ─────────────────────────────────────────────────────────
    public function lookupPatient(Request $request)
    {
        $phone      = $request->query('phone');
        $hospitalId = $request->query('hospital_id'); // optional — sent by in-person form

        if ($hospitalId) {
            // In-person form: only return patient if they have a booking under this hospital
            $exists = DB::table('bookings')
                ->where('patient_phone', $phone)
                ->where('hospital_id', $hospitalId)
                ->exists();

            if (!$exists) {
                return response()->json(['found' => false]);
            }
        }

        $patient = DB::table('patients')->where('phone_no', $phone)->first();

        if ($patient) {
            return response()->json([
                'found'   => true,
                'patient' => $patient,
            ]);
        }

        return response()->json(['found' => false]);
    }

    // ─────────────────────────────────────────────────────────
    // AJAX: Store public booking + upsert patient record
    // ─────────────────────────────────────────────────────────
    public function ajaxStore(Request $request, $doctorId)
    {
        $doctor = Doctor::findOrFail($doctorId);

        // Unique booking token
        do {
            $actionToken = 'BK-' . strtoupper(Str::random(8));
        } while (
            DB::table('bookings')->where('action_token', $actionToken)->exists()
        );

        // Upsert patient — matched on phone_no, all new fields included
        DB::table('patients')->updateOrInsert(
            [
                'phone_no' => $request->patient_phone,
            ],
            [
                'name'                   => $request->patient_name,
                'age'                    => $request->age                    ?: null,
                'gender'                 => $request->gender                 ?: null,
                'ic_passport_no'         => $request->ic_passport_no         ?: null,
                'dob'                    => $request->dob                    ?: null,
                'blood_type'             => $request->blood_type             ?: null,
                'marital_status'         => $request->marital_status         ?: null,
                'nationality'            => $request->nationality             ?: null,
                'address'                => $request->address                ?: null,
                'state'                  => $request->state                  ?: null,
                'city'                   => $request->city                   ?: null,
                'postcode'               => $request->postcode               ?: null,
                'country'                => $request->country                ?: null,
                'emergency_contact_name' => $request->emergency_contact_name ?: null,
                'emergency_contact_no'   => $request->emergency_contact_no   ?: null,
                'updated_at'             => now(),
                'created_at'             => now(),
            ]
        );

        // Insert booking record
        DB::table('bookings')->insert([
            'hospital_id'   => $doctor->hospital_id,
            'doctor_id'     => $doctorId,

            'patient_name'  => $request->patient_name,
            'patient_email' => $request->patient_email,
            'patient_phone' => $request->patient_phone,
            'age'           => $request->age   ?? null,
            'cause'         => $request->cause ?? null,

            'booking_date'  => $request->booking_date,
            'start_time'    => $request->start_time,
            'end_time'      => null,

            'status'        => 'pending',
            'action_token'  => $actionToken,

            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
        try {
    $hospital = \App\Models\Hospital::find($doctor->hospital_id);

    if ($hospital && !empty($hospital->token)) {
        $cleanPhone = preg_replace('/[^0-9+]/', '', $request->patient_phone);
        $token      = $hospital->token;

        if (!empty($cleanPhone)) {
            $client = new \GuzzleHttp\Client(['verify' => false]);

            // ── CALL 1: Create contact (ignore error if already exists) ──
            try {
                $client->post('https://app.speedbots.io/api/contacts', [
                    'headers' => [
                        'X-ACCESS-TOKEN' => $token,
                        'Content-Type'   => 'application/json',
                        'accept'         => 'application/json',
                    ],
                    'json'            => ['phone' => $cleanPhone],
                    'http_errors'     => false, // don't throw on 4xx/5xx
                ]);
            } catch (\Exception $e) {
                // Contact may already exist — continue to set custom fields
                \Illuminate\Support\Facades\Log::info('Speedbots contact create skipped (may exist)', [
                    'phone' => $cleanPhone, 'error' => $e->getMessage()
                ]);
            }

            // ── CALL 2: Set appointment date custom field ────
            try { $dateFieldId = $hospital->appointment_date_field_id ?? null; }
            catch (\Exception $e) { $dateFieldId = null; }

            if ($dateFieldId && $request->booking_date) {
                try {
                    $client->post("https://app.speedbots.io/api/contacts/{$cleanPhone}/custom_fields/{$dateFieldId}", [
                        'headers' => [
                            'X-ACCESS-TOKEN' => $token,
                            'Content-Type'   => 'application/x-www-form-urlencoded',
                            'accept'         => 'application/json',
                        ],
                        'form_params'  => ['value' => $request->booking_date],
                        'http_errors'  => false,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Speedbots date field failed', ['error' => $e->getMessage()]);
                }
            }

            // ── CALL 3: Set appointment time custom field ────
            try { $timeFieldId = $hospital->appointment_time_field_id ?? null; }
            catch (\Exception $e) { $timeFieldId = null; }

            if ($timeFieldId && $request->start_time) {
                try {
                    $formattedTime = \Carbon\Carbon::createFromFormat('H:i:s', $request->start_time)->format('h:i A');
                } catch (\Exception $e) {
                    $formattedTime = $request->start_time;
                }
                try {
                    $client->post("https://app.speedbots.io/api/contacts/{$cleanPhone}/custom_fields/{$timeFieldId}", [
                        'headers' => [
                            'X-ACCESS-TOKEN' => $token,
                            'Content-Type'   => 'application/x-www-form-urlencoded',
                            'accept'         => 'application/json',
                        ],
                        'form_params'  => ['value' => $formattedTime],
                        'http_errors'  => false,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Speedbots time field failed', ['error' => $e->getMessage()]);
                }
            }

            // ── CALL 4: Set booking code custom field ────
            try { $bookingCodeFieldId = $hospital->booking_code_field_id ?? null; }
            catch (\Exception $e) { $bookingCodeFieldId = null; }

            if ($bookingCodeFieldId && isset($actionToken)) {
                try {
                    $client->post("https://app.speedbots.io/api/contacts/{$cleanPhone}/custom_fields/{$bookingCodeFieldId}", [
                        'headers' => [
                            'X-ACCESS-TOKEN' => $token,
                            'Content-Type'   => 'application/x-www-form-urlencoded',
                            'accept'         => 'application/json',
                        ],
                        'form_params' => ['value' => $actionToken],
                        'http_errors' => false,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Speedbots booking code field failed', ['error' => $e->getMessage()]);
                }
            }
        }
    }
} catch (\Exception $e) {
    \Illuminate\Support\Facades\Log::error('Speedbots contact failed', [
        'phone' => $request->patient_phone,
        'error' => $e->getMessage(),
    ]);
}
        // 📧 SEND EMAIL (uncomment when ready)
        // Mail::to($request->patient_email)->send(
        //     new BookingVerificationMail($actionToken)
        // );

        return response()->json([
            'success'      => true,
            'booking_code' => $actionToken,
            'message'      => 'Booking confirmed',
        ]);
    }

    public function verify($token)
    {
        $booking = DB::table('bookings')
            ->where('action_token', $token)
            ->where('status', 'unverified')
            ->first();

        $id = DB::table('bookings')
            ->where('action_token', $token)
            ->first();

        if (!$id) {
            return response()->json('booking is not found');
        }

        if (!$booking) {
            return redirect()->route('booking.status', $token);
        }

        DB::table('bookings')
            ->where('id', $booking->id)
            ->update([
                'status'     => 'pending',
                'updated_at' => now(),
            ]);

        return redirect()->route('booking.status', $token);
    }

    public function status($code)
    {
        $booking = DB::table('bookings')
            ->join('doctors',   'doctors.id',   '=', 'bookings.doctor_id')
            ->join('hospitals', 'hospitals.id', '=', 'bookings.hospital_id')
            ->select(
                'bookings.*',
                'doctors.name             as doctor_name',
                'hospitals.hospital_name  as hospital_name'
            )
            ->where('bookings.action_token', $code)
            ->first();

        if (!$booking) {
            abort(404);
        }

        return view('users.booking_status', compact('booking'));
    }

    // ─────────────────────────────────────────────────────────
    // Private helpers (unchanged)
    // ─────────────────────────────────────────────────────────
    private function generateAvailableSlots($scheduleStart, $scheduleEnd, $bookings, $slotMinutes)
    {
        $slots = [];

        $start = Carbon::createFromFormat('H:i:s', $scheduleStart);
        $end   = Carbon::createFromFormat('H:i:s', $scheduleEnd);

        $start = $this->roundToSlot($start, $slotMinutes);

        while ($start < $end) {

            $slotStart = $start->format('H:i:s');
            $slotEnd   = $start->copy()->addMinutes($slotMinutes)->format('H:i:s');

            $blocked = false;

            foreach ($bookings as $booking) {

                // Pending → block only exact start
                if ($booking->end_time === null) {
                    if ($booking->start_time === $slotStart) {
                        $blocked = true;
                        break;
                    }
                }

                // Accepted → block range
                else {
                    if (
                        $slotStart < $booking->end_time &&
                        $slotEnd   > $booking->start_time
                    ) {
                        $blocked = true;
                        break;
                    }
                }
            }

            if (!$blocked) {
                $slots[] = [
                    'start' => $slotStart,
                    'end'   => $slotEnd,
                ];
            }

            $start->addMinutes($slotMinutes);
        }

        return $slots;
    }

    private function roundToSlot(Carbon $time, int $slotMinutes)
    {
        $minutes        = $time->minute;
        $roundedMinutes = floor($minutes / $slotMinutes) * $slotMinutes;

        return $time->copy()->minute($roundedMinutes)->second(0);
    }
}