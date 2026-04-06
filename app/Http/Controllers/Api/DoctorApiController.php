<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorApiController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/doctors",
     *     tags={"Doctors"},
     *     summary="List all active doctors for the hospital",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of doctors",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Doctor"))
     *         )
     *     )
     * )
     *
     * @OA\Schema(
     *     schema="Doctor",
     *     @OA\Property(property="id",               type="integer"),
     *     @OA\Property(property="name",             type="string"),
     *     @OA\Property(property="specialization",   type="string"),
     *     @OA\Property(property="qualification",    type="string"),
     *     @OA\Property(property="experience_years", type="integer"),
     *     @OA\Property(property="consultation_fee", type="number")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->get('_api_user');

        $doctors = DB::table('doctors as d')
            ->join('specializations as s', 's.id', '=', 'd.specialization_id')
            ->join('users as u', function ($join) {
                $join->on('u.doctor_id', '=', 'd.id')->where('u.status', 1);
            })
            ->where('d.hospital_id', $user->hospital_id)
            ->select(
                'd.id', 'd.name', 'd.qualification',
                'd.experience_years', 'd.consultation_fee',
                's.specialization'
            )
            ->get();

        return response()->json(['data' => $doctors]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/doctors/{id}/slots",
     *     tags={"Doctors"},
     *     summary="Get available time slots for a doctor on a given date",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id",   in="path",  required=true,  description="Doctor ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="date", in="query", required=true,  description="Date in YYYY-MM-DD format", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Available slots",
     *         @OA\JsonContent(
     *             @OA\Property(property="date",      type="string"),
     *             @OA\Property(property="doctor_id", type="integer"),
     *             @OA\Property(property="slots",     type="array", @OA\Items(
     *                 @OA\Property(property="start", type="string", example="09:00:00"),
     *                 @OA\Property(property="end",   type="string", example="09:10:00")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=404, description="Doctor not found or not available on this day")
     * )
     */
    public function slots(Request $request, int $id)
    {
        $user = $request->get('_api_user');

        $request->validate(['date' => 'required|date']);

        $date      = $request->date;
        $dayOfWeek = Carbon::parse($date)->format('l');

        $doctor = DB::table('doctors')->where('id', $id)->where('hospital_id', $user->hospital_id)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        $schedule = DB::table('schedules')
            ->where('doctor_id', $id)
            ->where('day', $dayOfWeek)
            ->where('is_off', 0)
            ->first();

        if (!$schedule) {
            return response()->json([
                'date'      => $date,
                'doctor_id' => $id,
                'slots'     => [],
                'message'   => 'Doctor is not available on this day.',
            ]);
        }

        $bookings = DB::table('bookings')
            ->where('doctor_id', $id)
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'accepted'])
            ->get();

        $slots = $this->generateSlots(
            $schedule->start_time,
            $schedule->end_time,
            $bookings,
            $doctor->slot ?? 10
        );

        return response()->json([
            'date'      => $date,
            'doctor_id' => $id,
            'slots'     => $slots,
        ]);
    }

    private function generateSlots(string $start, string $end, $bookings, int $slotMin): array
    {
        $slots    = [];
        $startDt  = Carbon::createFromFormat('H:i:s', $start);
        $endDt    = Carbon::createFromFormat('H:i:s', $end);
        $rounded  = (int) floor($startDt->minute / $slotMin) * $slotMin;
        $startDt  = $startDt->copy()->minute($rounded)->second(0);

        while ($startDt < $endDt) {
            $slotStart = $startDt->format('H:i:s');
            $slotEnd   = $startDt->copy()->addMinutes($slotMin)->format('H:i:s');
            $blocked   = false;

            foreach ($bookings as $b) {
                if ($b->end_time === null) {
                    if ($b->start_time === $slotStart) { $blocked = true; break; }
                } else {
                    if ($slotStart < $b->end_time && $slotEnd > $b->start_time) { $blocked = true; break; }
                }
            }

            if (!$blocked) {
                $slots[] = ['start' => $slotStart, 'end' => $slotEnd];
            }

            $startDt->addMinutes($slotMin);
        }

        return $slots;
    }
}