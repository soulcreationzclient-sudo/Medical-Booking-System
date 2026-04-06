<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingApiController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/bookings",
     *     tags={"Bookings"},
     *     summary="List all bookings for the hospital",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="date",   in="query", description="Filter by date (YYYY-MM-DD)",              required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="status", in="query", description="Filter by status (pending/accepted/etc.)", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="doctor_id", in="query", description="Filter by doctor ID",                  required=false, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="List of bookings",
     *         @OA\JsonContent(
     *             @OA\Property(property="data",  type="array", @OA\Items(ref="#/components/schemas/Booking")),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * @OA\Schema(
     *     schema="Booking",
     *     @OA\Property(property="id",            type="integer"),
     *     @OA\Property(property="action_token",  type="string",  example="BK-XK92MPLR"),
     *     @OA\Property(property="patient_name",  type="string"),
     *     @OA\Property(property="patient_phone", type="string"),
     *     @OA\Property(property="booking_date",  type="string",  example="2026-03-29"),
     *     @OA\Property(property="start_time",    type="string",  example="10:30:00"),
     *     @OA\Property(property="status",        type="string",  example="pending"),
     *     @OA\Property(property="doctor_name",   type="string"),
     *     @OA\Property(property="cause",         type="string")
     * )
     */
    public function index(Request $request)
    {
        $user       = $request->get('_api_user');
        $hospitalId = $user->hospital_id;

        $query = DB::table('bookings as b')
            ->leftJoin('doctors as d', 'd.id', '=', 'b.doctor_id')
            ->where('b.hospital_id', $hospitalId)
            ->select(
                'b.id', 'b.action_token', 'b.patient_name', 'b.patient_phone',
                'b.patient_email', 'b.booking_date', 'b.start_time', 'b.status',
                'b.cause', 'b.created_at', 'd.name as doctor_name'
            );

        if ($request->filled('date')) {
            $query->whereDate('b.booking_date', $request->date);
        }
        if ($request->filled('status')) {
            $query->where('b.status', $request->status);
        }
        if ($request->filled('doctor_id')) {
            $query->where('b.doctor_id', $request->doctor_id);
        }

        $bookings = $query->latest('b.created_at')->get();

        return response()->json([
            'data'  => $bookings,
            'total' => $bookings->count(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/bookings/{id}",
     *     tags={"Bookings"},
     *     summary="Get a single booking by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200,  description="Booking details", @OA\JsonContent(ref="#/components/schemas/Booking")),
     *     @OA\Response(response=404,  description="Not found")
     * )
     */
    public function show(Request $request, int $id)
    {
        $user = $request->get('_api_user');

        $booking = DB::table('bookings as b')
            ->leftJoin('doctors as d', 'd.id', '=', 'b.doctor_id')
            ->where('b.id', $id)
            ->where('b.hospital_id', $user->hospital_id)
            ->select('b.*', 'd.name as doctor_name')
            ->first();

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        return response()->json($booking);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/bookings",
     *     tags={"Bookings"},
     *     summary="Create a new booking (in-person / admin)",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"patient_name","patient_phone","doctor_id","booking_date","start_time"},
     *             @OA\Property(property="patient_name",  type="string",  example="Vijay Kumar"),
     *             @OA\Property(property="patient_phone", type="string",  example="919994780436"),
     *             @OA\Property(property="patient_email", type="string",  example="vijay@email.com"),
     *             @OA\Property(property="doctor_id",     type="integer", example=1),
     *             @OA\Property(property="booking_date",  type="string",  example="2026-03-29"),
     *             @OA\Property(property="start_time",    type="string",  example="10:30"),
     *             @OA\Property(property="age",           type="integer", example=30),
     *             @OA\Property(property="cause",         type="string",  example="Fever and headache")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Booking created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success",       type="boolean"),
     *             @OA\Property(property="booking_code",  type="string", example="BK-XK92MPLR"),
     *             @OA\Property(property="message",       type="string")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $user = $request->get('_api_user');

        $validated = $request->validate([
            'patient_name'  => 'required|string',
            'patient_phone' => 'required|string',
            'patient_email' => 'nullable|email',
            'doctor_id'     => 'required|integer',
            'booking_date'  => 'required|date|after_or_equal:today',
            'start_time'    => 'required|date_format:H:i',
            'age'           => 'nullable|integer',
            'cause'         => 'nullable|string',
        ]);

        // Verify doctor belongs to this hospital
        $doctor = Doctor::where('id', $validated['doctor_id'])
            ->where('hospital_id', $user->hospital_id)
            ->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found at your hospital'], 404);
        }

        do {
            $token = 'BK-' . strtoupper(Str::random(8));
        } while (DB::table('bookings')->where('action_token', $token)->exists());

        DB::table('patients')->updateOrInsert(
            ['phone_no' => $validated['patient_phone']],
            ['name' => $validated['patient_name'], 'updated_at' => now(), 'created_at' => now()]
        );

        DB::table('bookings')->insert([
            'hospital_id'   => $user->hospital_id,
            'doctor_id'     => $validated['doctor_id'],
            'patient_name'  => $validated['patient_name'],
            'patient_email' => $validated['patient_email'] ?? null,
            'patient_phone' => $validated['patient_phone'],
            'age'           => $validated['age'] ?? null,
            'cause'         => $validated['cause'] ?? null,
            'booking_date'  => $validated['booking_date'],
            'start_time'    => $validated['start_time'] . ':00',
            'end_time'      => null,
            'status'        => 'pending',
            'action_token'  => $token,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response()->json([
            'success'      => true,
            'booking_code' => $token,
            'message'      => 'Booking created successfully',
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/bookings/{id}/status",
     *     tags={"Bookings"},
     *     summary="Update booking status (accept / reject / complete etc.)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"pending","accepted","rejected","cancelled","completed","no_show"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status updated"),
     *     @OA\Response(response=404, description="Booking not found")
     * )
     */
    public function updateStatus(Request $request, int $id)
    {
        $user = $request->get('_api_user');

        $request->validate([
            'status' => 'required|in:pending,accepted,rejected,cancelled,completed,no_show',
        ]);

        $booking = Booking::where('id', $id)
            ->where('hospital_id', $user->hospital_id)
            ->first();

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $oldStatus       = $booking->status;
        $booking->status = $request->status;
        $booking->save();

        return response()->json([
            'success'    => true,
            'old_status' => $oldStatus,
            'new_status' => $booking->status,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/bookings/{id}/reschedule",
     *     tags={"Bookings"},
     *     summary="Reschedule a booking to a new date and time",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"new_date","new_time"},
     *             @OA\Property(property="new_date",  type="string", example="2026-04-01"),
     *             @OA\Property(property="new_time",  type="string", example="14:30"),
     *             @OA\Property(property="reason",    type="string", example="Doctor unavailable")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Rescheduled successfully"),
     *     @OA\Response(response=404, description="Booking not found")
     * )
     */
    public function reschedule(Request $request, int $id)
    {
        $user = $request->get('_api_user');

        $validated = $request->validate([
            'new_date' => 'required|date|after_or_equal:today',
            'new_time' => 'required|date_format:H:i',
            'reason'   => 'nullable|string|max:500',
        ]);

        $booking = Booking::where('id', $id)
            ->where('hospital_id', $user->hospital_id)
            ->first();

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $oldDate = $booking->booking_date;
        $oldTime = $booking->start_time;

        $booking->booking_date      = $validated['new_date'];
        $booking->start_time        = $validated['new_time'];
        $booking->status            = 'rescheduled';
        $booking->rescheduled_at    = now();
        $booking->rescheduled_by    = $user->id;
        $booking->reschedule_reason = $validated['reason'] ?? null;
        $booking->save();

        return response()->json([
            'success'  => true,
            'old_date' => $oldDate,
            'old_time' => $oldTime,
            'new_date' => $booking->booking_date,
            'new_time' => $booking->start_time,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/bookings/status/{code}",
     *     tags={"Bookings"},
     *     summary="Get booking status by booking code (public — no auth needed)",
     *     @OA\Parameter(name="code", in="path", required=true, description="Booking code e.g. BK-XK92MPLR", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Booking status", @OA\JsonContent(ref="#/components/schemas/Booking")),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function statusByCode(string $code)
    {
        $booking = DB::table('bookings as b')
            ->join('doctors as d',   'd.id',   '=', 'b.doctor_id')
            ->join('hospitals as h', 'h.id',   '=', 'b.hospital_id')
            ->where('b.action_token', $code)
            ->select(
                'b.action_token', 'b.patient_name', 'b.booking_date',
                'b.start_time', 'b.status', 'b.cause',
                'd.name as doctor_name', 'h.hospital_name'
            )
            ->first();

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        return response()->json($booking);
    }
}