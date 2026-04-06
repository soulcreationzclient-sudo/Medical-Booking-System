<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientApiController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/patients",
     *     tags={"Patients"},
     *     summary="List all patients who have bookings at this hospital",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="search", in="query", description="Search by name or phone", required=false, @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="List of patients",
     *         @OA\JsonContent(
     *             @OA\Property(property="data",  type="array", @OA\Items(ref="#/components/schemas/Patient")),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     *
     * @OA\Schema(
     *     schema="Patient",
     *     @OA\Property(property="id",       type="integer"),
     *     @OA\Property(property="name",     type="string"),
     *     @OA\Property(property="phone_no", type="string"),
     *     @OA\Property(property="age",      type="integer"),
     *     @OA\Property(property="gender",   type="string"),
     *     @OA\Property(property="nationality", type="string")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->get('_api_user');

        $query = DB::table('patients as p')
            ->join('bookings as b', 'b.patient_phone', '=', 'p.phone_no')
            ->where('b.hospital_id', $user->hospital_id)
            ->select('p.id', 'p.name', 'p.phone_no', 'p.age', 'p.gender', 'p.nationality', 'p.ic_passport_no')
            ->distinct();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('p.name', 'like', "%{$search}%")
                  ->orWhere('p.phone_no', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('p.name')->get();

        return response()->json(['data' => $patients, 'total' => $patients->count()]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/patients/{id}",
     *     tags={"Patients"},
     *     summary="Get a patient's full profile including booking history",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Patient profile with bookings"),
     *     @OA\Response(response=404, description="Patient not found")
     * )
     */
    public function show(Request $request, int $id)
    {
        $user    = $request->get('_api_user');
        $patient = DB::table('patients')->where('id', $id)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        // Verify patient has bookings at this hospital
        $hasBooking = DB::table('bookings')
            ->where('patient_phone', $patient->phone_no)
            ->where('hospital_id', $user->hospital_id)
            ->exists();

        if (!$hasBooking) {
            return response()->json(['error' => 'Patient not found at your hospital'], 404);
        }

        $bookings = DB::table('bookings as b')
            ->leftJoin('doctors as d', 'd.id', '=', 'b.doctor_id')
            ->where('b.patient_phone', $patient->phone_no)
            ->where('b.hospital_id', $user->hospital_id)
            ->select('b.id', 'b.action_token', 'b.booking_date', 'b.start_time', 'b.status', 'b.cause', 'd.name as doctor_name')
            ->latest('b.created_at')
            ->get();

        return response()->json([
            'patient'  => $patient,
            'bookings' => $bookings,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/patients/lookup",
     *     tags={"Patients"},
     *     summary="Look up a patient by phone number",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="phone", in="query", required=true, description="Phone number with country code", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Patient found or not found", @OA\JsonContent(
     *         @OA\Property(property="found",   type="boolean"),
     *         @OA\Property(property="patient", ref="#/components/schemas/Patient")
     *     ))
     * )
     */
    public function lookup(Request $request)
    {
        $user  = $request->get('_api_user');
        $phone = $request->query('phone');

        if (!$phone) {
            return response()->json(['error' => 'Phone number required'], 422);
        }

        $exists = DB::table('bookings')
            ->where('patient_phone', $phone)
            ->where('hospital_id', $user->hospital_id)
            ->exists();

        if (!$exists) {
            return response()->json(['found' => false]);
        }

        $patient = DB::table('patients')->where('phone_no', $phone)->first();

        return response()->json([
            'found'   => (bool) $patient,
            'patient' => $patient,
        ]);
    }
}