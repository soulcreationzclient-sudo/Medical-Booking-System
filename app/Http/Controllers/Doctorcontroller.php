<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class Doctorcontroller extends Controller
{
    /**
     * Home page
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Display doctor's schedule
     */
    public function schedule()
    {
        $doctorId = auth()->user()->doctor_id;

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $hours = Schedule::where('doctor_id', $doctorId)
            ->get()
            ->keyBy('day');

        return view('doctors.schedule', compact('days', 'hours'));
    }

    /**
     * Save doctor's schedule
     */
    public function schedule_save(Request $request)
    {
        $doctorId = auth()->user()->doctor_id;

        DB::beginTransaction();
        try {
            foreach ($request->schedule as $day => $data) {
                Schedule::updateOrCreate(
                    [
                        'doctor_id' => $doctorId,
                        'day' => $day
                    ],
                    [
                        'start_time' => $data['start_time'] ?? null,
                        'end_time' => $data['end_time'] ?? null,
                        'is_off' => (empty($data['start_time']) || empty($data['end_time']) || isset($data['is_off'])) ? 1 : 0,
                        'updated_at' => now(),
                    ]
                );
            }
            DB::commit();
            return back()->with('success', 'Schedule saved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Schedule save failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to save schedule');
        }
    }

    /**
     * Display overall bookings with filters
     */
    public function overall_bookings(Request $request)
    {
        $doctorId = auth()->user()->doctor_id;

        // Base query with eager loading for optimization
        $query = Booking::where('doctor_id', $doctorId);

        // Date search
        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        // Filter logic
        switch ($request->filter) {
            case 'today':
                $query->whereDate('booking_date', today());
                break;

            case 'unverified':
                $query->where('status', 'unverified')->latest()->limit(20);
                break;

            case 'pending':
                $query->where('status', 'pending');
                break;

            case 'accepted':
                $query->where('status', 'accepted')->latest()->limit(20);
                break;

            case 'rejected':
                $query->where('status', 'rejected')->latest()->limit(20);
                break;

            case 'cancelled':
                $query->where('status', 'cancelled')->latest()->limit(20);
                break;

            case 'no_show':
                $query->where('status', 'no_show')->latest()->limit(20);
                break;

            case 'rescheduled':
                $query->where('status', 'rescheduled')->latest()->limit(20);
                break;

            case 'completed':
                $query->where('status', 'completed')->latest()->limit(20);
                break;

            default:
                // Default: pending + today
                $query->where(function ($q) {
                    $q->where('status', 'pending')
                        ->orWhereDate('booking_date', today());
                });
                break;
        }

        $booking_list = $query->latest()->get();

        return view('doctors.overall_bookings', compact('booking_list'));
    }

    /**
     * Update booking status (Accept, Reject, No Show, Completed)
     */
    public function update_status(Request $request, $id)
    {
        // Comprehensive validation
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in(['unverified', 'pending', 'accepted', 'rejected', 'cancelled', 'no_show', 'completed'])
            ]
        ]);

        try {
            $booking = Booking::findOrFail($id);

            // Authorization check
            if ($booking->doctor_id != auth()->user()->doctor_id) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Unauthorized: You cannot modify this booking'
                ], 403);
            }

            // Store old status for logging
            $oldStatus = $booking->status;

            // Update status
            $booking->status = $validated['status'];

            // Store completion timestamp if status is completed
            if ($validated['status'] === 'completed') {
                $booking->completed_at = now();
            }

            $booking->save();

            // Send notification via Speedbots API
            $this->sendSpeedbotsNotification($booking, $oldStatus, $validated['status']);

            Log::channel('doctor')->info('Booking status updated', [
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'doctor_id' => auth()->user()->doctor_id
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Booking status updated successfully',
                'data' => [
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status']
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Booking not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Update status failed', [
                'booking_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'msg' => 'Failed to update status. Please try again.'
            ], 500);
        }
    }

    /**
     * Reschedule booking with comprehensive validation
     */
    public function reschedule(Request $request, $id)
    {
        // Strong validation
        $validated = $request->validate([
            'new_date' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'new_time' => [
                'required',
                'date_format:H:i'
            ],
            'reason' => [
                'nullable',
                'string',
                'max:500'
            ]
        ], [
            'new_date.required' => 'New appointment date is required',
            'new_date.after_or_equal' => 'New date must be today or a future date',
            'new_time.required' => 'New appointment time is required',
            'new_time.date_format' => 'Time must be in HH:MM format (e.g., 14:30)',
            'reason.max' => 'Reason must not exceed 500 characters'
        ]);

        DB::beginTransaction();
        try {
            $booking = Booking::findOrFail($id);

            // Authorization check
            $doctorId = auth()->user()->doctor_id;
            if ($booking->doctor_id !== $doctorId) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Unauthorized: You cannot reschedule this booking'
                ], 403);
            }

            // Prevent rescheduling completed bookings
            if ($booking->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'msg' => 'Cannot reschedule a completed booking'
                ], 400);
            }

            // Store old date/time for reference
            $oldDate = $booking->booking_date;
            $oldTime = $booking->start_time;
            $oldStatus = $booking->status;

            // Update booking details
            $booking->booking_date = $validated['new_date'];
            $booking->start_time = $validated['new_time'];
            $booking->status = 'rescheduled';

            // Store reschedule metadata
            if (!empty($validated['reason'])) {
                $booking->reschedule_reason = $validated['reason'];
            }

            $booking->rescheduled_at = now();
            $booking->rescheduled_by = auth()->id();

            $booking->save();

            // Send notification via Speedbots API
            $this->sendSpeedbotsNotification($booking, $oldStatus, 'rescheduled', [
                'old_date' => $oldDate,
                'old_time' => $oldTime,
                'new_date' => $validated['new_date'],
                'new_time' => $validated['new_time'],
                'reason' => $validated['reason'] ?? null
            ]);

            DB::commit();

            Log::channel('doctor')->info('Booking rescheduled successfully', [
                'booking_id' => $booking->id,
                'old_date' => $oldDate,
                'old_time' => $oldTime,
                'new_date' => $validated['new_date'],
                'new_time' => $validated['new_time'],
                'doctor_id' => $doctorId
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Appointment rescheduled successfully',
                'data' => [
                    'old_date' => $oldDate,
                    'old_time' => $oldTime,
                    'new_date' => $booking->booking_date,
                    'new_time' => $booking->start_time,
                    'reason' => $validated['reason'] ?? null
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'msg' => 'Booking not found'
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'msg' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Reschedule failed', [
                'booking_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'msg' => 'Failed to reschedule appointment. Please try again.'
            ], 500);
        }
    }

    /**
     * Add/Update doctor's slot duration
     */
    public function add_slot(Request $request)
    {
        $validated = $request->validate([
            'slot' => [
                'required',
                'numeric',
                'min:5',
                'max:120'
            ]
        ], [
            'slot.required' => 'Slot duration is required',
            'slot.numeric' => 'Slot duration must be a number',
            'slot.min' => 'Slot duration must be at least 5 minutes',
            'slot.max' => 'Slot duration cannot exceed 120 minutes'
        ]);

        try {
            $roundedSlot = ceil($validated['slot']);

            $updated = Doctor::where('id', auth()->user()->doctor_id)
                ->update(['slot' => $roundedSlot]);

            if ($updated) {
                Log::info('Slot duration updated', [
                    'doctor_id' => auth()->user()->doctor_id,
                    'slot' => $roundedSlot
                ]);

                return back()->with('success', "Slot duration set to {$roundedSlot} minutes");
            }

            return back()->with('error', 'Failed to set slot duration');

        } catch (\Exception $e) {
            Log::error('Add slot failed: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while setting slot duration');
        }
    }

    /**
     * Send Speedbots notification with comprehensive validation and error handling
     *
     * @param Booking $booking
     * @param string $oldStatus
     * @param string $newStatus
     * @param array $additionalData
     * @return void
     */
    private function sendSpeedbotsNotification(Booking $booking, string $oldStatus, string $newStatus, array $additionalData = [])
    {
        try {
            // Validate hospital exists
            if (!$booking->hospital_id) {
                Log::channel('doctor')->warning('Speedbots notification skipped: No hospital_id', [
                    'booking_id' => $booking->id
                ]);
                return;
            }

            // Fetch hospital with necessary credentials
            $hospital = Hospital::find($booking->hospital_id);

            // Comprehensive validation
            if (!$hospital) {
                Log::channel('doctor')->warning('Speedbots notification skipped: Hospital not found', [
                    'booking_id' => $booking->id,
                    'hospital_id' => $booking->hospital_id
                ]);
                return;
            }

            // Validate required credentials
            if (empty($hospital->token)) {
                Log::channel('doctor')->warning('Speedbots notification skipped: Missing API token', [
                    'booking_id' => $booking->id,
                    'hospital_id' => $hospital->id
                ]);
                return;
            }

            if (empty($hospital->flow_id)) {
                Log::channel('doctor')->warning('Speedbots notification skipped: Missing flow_id', [
                    'booking_id' => $booking->id,
                    'hospital_id' => $hospital->id
                ]);
                return;
            }

            // Validate patient phone number
            if (empty($booking->patient_phone)) {
                Log::channel('doctor')->warning('Speedbots notification skipped: Missing patient phone', [
                    'booking_id' => $booking->id
                ]);
                return;
            }

            // Clean and validate phone number format
            $contactId = preg_replace('/[^0-9+]/', '', $booking->patient_phone);

            if (empty($contactId)) {
                Log::channel('doctor')->warning('Speedbots notification skipped: Invalid phone format', [
                    'booking_id' => $booking->id,
                    'phone' => $booking->patient_phone
                ]);
                return;
            }

            // Send notification via Speedbots API
            Log::channel('doctor')->info('Sending Speedbots notification', [
                'booking_id' => $booking->id,
                'contact_id' => $contactId,
                'flow_id' => $hospital->flow_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            // Update status custom field (591719)


// Update date custom field (244056)
$dateResponse = Http::timeout(10)
    ->withHeaders([
        'X-ACCESS-TOKEN' => $hospital->token,
        'Content-Type' => 'application/x-www-form-urlencoded'
    ])
    ->asForm()
    ->post("https://app.speedbots.io/api/contacts/{$contactId}/custom_fields/244056", [
        'value' => $booking->booking_date
    ]);

// Update message custom field (947818)
$response = Http::timeout(10)
    ->withHeaders([
        'X-ACCESS-TOKEN' => $hospital->token,
        'Content-Type' => 'application/x-www-form-urlencoded'
    ])
    ->asForm()
    ->post("https://app.speedbots.io/api/contacts/{$contactId}/custom_fields/947818", [
        'value' => $booking->patient_name
    ]);
    $statusResponse = Http::timeout(10)
    ->withHeaders([
        'X-ACCESS-TOKEN' => $hospital->token,
        'Content-Type' => 'application/x-www-form-urlencoded'
    ])
    ->asForm()
    ->post("https://app.speedbots.io/api/contacts/{$contactId}/custom_fields/591719", [
        'value' => $newStatus
    ]);

            // Log response
            if ($response->successful()) {
                Log::channel('doctor')->info('Speedbots notification sent successfully', [
                    'booking_id' => $booking->id,
                    'status_code' => $response->status(),
                    'response' => $response->json()
                ]);
            } else {
                Log::channel('doctor')->error('Speedbots notification failed', [
                    'booking_id' => $booking->id,
                    'status_code' => $response->status(),
                    'error' => $response->body()
                ]);
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::channel('doctor')->error('Speedbots connection error', [
                'booking_id' => $booking->id,
                'error' => 'Connection timeout or network error',
                'message' => $e->getMessage()
            ]);

        } catch (\Exception $e) {
            Log::channel('doctor')->error('Speedbots notification exception', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    public function calendar(Request $request)
{
    $doctorId = auth()->user()->doctor_id;
 
    $currentMonth = $request->filled('month')
        ? \Carbon\Carbon::parse($request->month . '-01')
        : now()->startOfMonth();
 
    $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
    $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');
 
    $startDate = $currentMonth->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
    $endDate   = $currentMonth->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
 
    $bookings = \App\Models\Booking::where('doctor_id', $doctorId)
        ->whereBetween('booking_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->orderBy('start_time')
        ->get();
 
    $bookingsByDate = $bookings->groupBy(fn($b) => \Carbon\Carbon::parse($b->booking_date)->toDateString());
 
    $weeks  = [];
    $cursor = $startDate->copy();
 
    while ($cursor <= $endDate) {
        $week = [];
        for ($i = 0; $i < 7; $i++) {
            $dateStr = $cursor->toDateString();
            $week[] = [
                'date'     => $cursor->copy(),
                'inMonth'  => $cursor->month === $currentMonth->month,
                'bookings' => $bookingsByDate->get($dateStr, collect()),
            ];
            $cursor->addDay();
        }
        $weeks[] = $week;
    }
 
    $allBookings = $bookings->map(fn($b) => [
        'id'           => $b->id,
        'patient_name' => $b->patient_name,
        'patient_phone'=> $b->patient_phone,
        'booking_date' => \Carbon\Carbon::parse($b->booking_date)->toDateString(),
        'start_time'   => $b->start_time,
        'status'       => $b->status,
        'cause'        => $b->cause,
        'action_token' => $b->action_token,
    ])->values();
 
    return view('doctors.calendar', compact(
        'weeks', 'currentMonth', 'prevMonth', 'nextMonth', 'allBookings'
    ));
}
  
}
