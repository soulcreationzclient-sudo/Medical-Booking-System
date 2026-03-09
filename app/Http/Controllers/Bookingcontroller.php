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
    //
    public function booking(Request $request, $doctorId)
    {
        $date = $request->get('date');
        $phone_no=$request->get('phone_no')??null;
        $doctor=Doctor::findOrFail($doctorId);
        $day = \Carbon\Carbon::parse($date)->format('l');

        $schedule = DB::table('schedules')
            ->where('doctor_id', $doctorId)
            ->where('day', $day)
            // ->where('is_off', 0)
            ->first();
        if(!$schedule){
            return back()->with('error', 'Doctor not available on this day');
        }
        // Log::channel('doctor')->info("$schedule");
        // pr($schedule);
        if ($schedule->is_off==1) {
            return back()->with('error', 'Doctor not available on this day');
        }

        $bookings = DB::table('bookings')
            ->where('doctor_id', $doctorId)
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'accepted'])
            ->get();
        // pr($schedule);
        $slots = $this->generateAvailableSlots(
            $schedule->start_time,
            $schedule->end_time,
            $bookings,
            $doctor->slot??10
        );

        return view('users.booking_form', compact(
            'doctorId',
            'date',
            'slots',
            'phone_no'
        ));
    }

    private function generateAvailableSlots($scheduleStart, $scheduleEnd, $bookings, $slotMinutes)
    {
        $slots = [];

        $start = Carbon::createFromFormat('H:i:s', $scheduleStart);
        $end   = Carbon::createFromFormat('H:i:s', $scheduleEnd);

        // 🔥 ROUND schedule start
        $start = $this->roundToSlot($start, $slotMinutes);

        while ($start < $end) {

            $slotStart = $start->format('H:i:s');
            $slotEnd   = $start->copy()->addMinutes($slotMinutes)->format('H:i:s');

            $blocked = false;

            foreach ($bookings as $booking) {

                // 🔒 Pending → block only exact start
                if ($booking->end_time === null) {
                    if ($booking->start_time === $slotStart) {
                        $blocked = true;
                        break;
                    }
                }

                // 🔒 Accepted → block range
                else {
                    if (
                        $slotStart < $booking->end_time &&
                        $slotEnd > $booking->start_time
                    ) {
                        $blocked = true;
                        break;
                    }
                }
            }

            if (!$blocked) {
                $slots[] = [
                    'start' => $slotStart,
                    'end'   => $slotEnd
                ];
            }

            $start->addMinutes($slotMinutes);
        }

        return $slots;
    }

    private function roundToSlot(Carbon $time, int $slotMinutes)
    {
        $minutes = $time->minute;
        $roundedMinutes = floor($minutes / $slotMinutes) * $slotMinutes;

        return $time->copy()->minute($roundedMinutes)->second(0);
    }
    public function ajaxStore(Request $request, $doctorId)
    {
        $doctor = Doctor::findOrFail($doctorId);

        // Unique token
        do {
            $actionToken = 'BK-' . strtoupper(Str::random(8));
        } while (
            DB::table('bookings')->where('action_token', $actionToken)->exists()
        );
        DB::table('patients')->updateOrInsert(
            ['phone_no'=>$request->patient_phone],
            [
            'name'=>$request->patient_name,
            'age'=>$request->age,
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        DB::table('bookings')->insert([
            'hospital_id'   => $doctor->hospital_id,
            'doctor_id'     => $doctorId,

            'patient_name'  => $request->patient_name,
            'patient_email' => $request->patient_email,
            'patient_phone' => $request->patient_phone,
            'age' => $request->age ?? null,
            'cause' => $request->cause ?? null,

            'booking_date'  => $request->booking_date,
            'start_time'    => $request->start_time,
            'end_time'      => null,

            // 🔥 IMPORTANT CHANGE
            'status'        => 'pending',
            'action_token'  => $actionToken,

            'created_at'    => now(),
            'updated_at'    => now(),
        ]);


        // 📧 SEND EMAIL
        // Mail::to($request->patient_email)->send(
        //     new BookingVerificationMail($actionToken)
        // );

        return response()->json([
            'success' => true,
            'booking_code' => $actionToken,
            'message' => 'Booking confirmed'
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
        if(!$id){
            return response()->json('booking is not found');
        }
    if (!$booking) {
        return redirect()->route('booking.status', $token);
    }

    DB::table('bookings')
        ->where('id', $booking->id)
        ->update([
            'status' => 'pending',
            'updated_at' => now()
        ]);

    return redirect()->route('booking.status', $token);
}


    public function status($code)
    {
        $booking = DB::table('bookings')
            ->join('doctors', 'doctors.id', '=', 'bookings.doctor_id')
            ->join('hospitals', 'hospitals.id', '=', 'bookings.hospital_id')
            ->select(
                'bookings.*',
                'doctors.name as doctor_name',
                'hospitals.hospital_name as hospital_name'
            )
            ->where('bookings.action_token', $code)
            ->first();
        // pr($booking);

        if (!$booking) {
            abort(404);
        }

        return view('users.booking_status', compact('booking'));
    }
}
