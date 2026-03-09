<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inpersonrequest;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Hospitaladmincontroller extends Controller
{
    //
    public function index()
    {
        return view('home');
    }
    public function add_doctor_view()
    {

        $doctors = DB::table('doctors as d')->join('specializations as s', 's.id', '=', 'd.specialization_id')->where('d.hospital_id', auth()->user()->hospital_id)->select(
            'd.id as id',
            'd.name as name',
            'd.profile_photo',
            'd.doctor_code',
            'd.qualification',
            's.specialization',
            'd.phone'
        )->get();
        // pr($doctor);
        return view('hospital_admin.hospital_admin_dashboard', compact('doctors'));
    }
    public function specialization_view()
    {
        $specialization = Specialization::where('hospital_id', auth()->user()->hospital->id)->get();
        // pr($specialization->toArray());
        return view('hospital_admin.specialization', compact('specialization'));
    }
    public function specialization_add(Request $request)
    {
        // return 'hi';
        if (empty($request->specialization)) {
            return back()->with('error', 'Must enter specialization type');
        }
        $id = auth()->user()->hospital?->id;
        if (!$id) {
            return back()->with('error', 'Hospital not found');
        }
        Specialization::create([
            'hospital_id' => $id,
            'specialization' => $request->specialization,
            'description' => $request->description ?? null,
        ]);
        return back()->with('success', 'Specialization added');
    }
    public function specialization_delete(Request $request)
    {
        if (!$request->id) {
            return response()->json([
                'success' => false,
                'message' => 'Missing specialization ID'
            ], 422);
        }

        $specialization = Specialization::find($request->id);

        if (!$specialization) {
            return response()->json([
                'success' => false,
                'message' => 'Specialization not found'
            ], 404);
        }
        $check = $this->updatedelete_check($request->id);
        if (!$check) {
            return response()->json([
                'success' => false,
                'msg' => 'Authorization error'
            ], 403);
        }

        if ($specialization->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Specialization deleted'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to delete specialization'
        ], 500);
    }
    public function specialization_edit(Request $request)
    {
        // pr($request->description);
        if (empty($request->id)) {
            return back()->with('error', 'Specialization ID is required');
        } elseif (empty($request->specialization)) {
            return back()->with('error', 'Specialization name is required');
        } elseif (empty($request->description)) {
            return back()->with('error', 'Specialization description is required');
        } else {
            $specialization = $this->updatedelete_check($request->id);
            if (!$specialization) {
                abort(403);
            } else {
                $specialization->update([
                    'specialization' => $request->specialization,
                    'description' => $request->description,
                ]);
                return back()->with('success', 'Specialization updated');
            }
        }
    }
    public function updatedelete_check($id)
    {
        $specialization = Specialization::find($id);

        if (!$specialization) {
            return false;
        }

        if ($specialization->hospital_id !== auth()->user()->hospital_id) {
            return false;
        }

        return $specialization;
    }
    public function doctor_form()
    {
        $specialization = Specialization::where('hospital_id', auth()->user()->hospital_id)->get();
        return view('hospital_admin.doctor_form', [
            'route' => 'hospital_admin.doctor_add',
            'title' => 'Create doctor',
            'button' => 'Submit',
            'specialization' => $specialization
        ]);
    }

    public function doctor_add(Request $request)
    {
        $id = $request->id;
        // pr($request->status);
        $user_id = null;
        if ($id) {
            $user_id = User::where('doctor_id', $id)
                ->where('hospital_id', auth()->user()->hospital_id)
                ->value('id');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user_id),
            ],
            'phone' => 'required|string|max:20',
            'gender' => 'required',
            'experience_years' => 'required|numeric',
            'specialization' => 'required',
            'qualification' => 'required|string',
            'status' => 'nullable',
            'profile_photo' => 'nullable|image|max:2048',
        ];

        if ($id) {
            Log::channel('hospital_admin')->info('Doctor update flow', ['doctor_id' => $id]);
            if ($request->filled('password')) {
                $rules['password'] = 'min:6';
            }
        } else {
            Log::channel('hospital_admin')->info('Doctor create flow');
            $rules['password'] = 'required|min:6';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {

            $doctor = $id ? Doctor::findOrFail($id) : new Doctor();

            if ($request->hasFile('profile_photo')) {
                if ($id && $doctor->profile_photo) {
                    Storage::disk('s3')->delete($doctor->profile_photo);
                }

                $photo = $request->file('profile_photo');
                $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                Storage::disk('s3')->putFileAs('doctors', $photo, $filename);
                $doctor->profile_photo = 'doctors/' . $filename;
            }

            $doctor->fill([
                'name' => $validated['name'],
                'hospital_id' => auth()->user()->hospital_id,
                'gender' => $validated['gender'],
                'doctor_code' => $id ? $doctor->doctor_code : $this->doctorcode(),
                'experience_years' => $validated['experience_years'],
                'phone' => $validated['phone'],
                'specialization_id' => $validated['specialization'],
                'qualification' => $validated['qualification'],
            ]);

            $doctor->save();
            $user = User::where('doctor_id', $doctor->id)
                ->where('hospital_id', auth()->user()->hospital_id)
                ->first();

            if (!$user) {
                $user = new User();
                $user->doctor_id = $doctor->id;
                $user->api_code = $this->uniquecode('user');
            }

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role = 'doctor';
            $user->hospital_id = auth()->user()->hospital_id;
            $user->status = $request->has('status') ? 1 : 0;

            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }

            $user->save();


            DB::commit();

            Log::channel('hospital_admin')->info(
                $id ? 'Doctor updated successfully' : 'Doctor created successfully',
                [
                    'doctor_id' => $doctor->id,
                    'user_id' => $user->id,
                ]
            );

            return back()->with(
                'success',
                $id ? 'Doctor updated successfully' : 'Doctor created successfully'
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::channel('hospital_admin')->error('Doctor save failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'doctor_id' => $id,
            ]);

            return back()->with(
                'error',
                'Something went wrong while saving doctor details. Please try again.'
            );
        }
    }

    public function doctor_delete(Request $request)
    {
        if (!$request->id) {
            return response()->json([
                'success' => false,
                'message' => 'Missing doctor ID'
            ], 422);
        }

        $doctor = Doctor::find($request->id);

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found'
            ], 404);
        }

        if (!$this->canModifyDoctor($doctor)) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization error'
            ], 403);
        }
        @Storage::disk('s3')->delete($doctor->profile_photo);
        $doctor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Doctor deleted successfully'
        ]);
    }
    public function edit_doctor_view($id)
    {
        $specialization = Specialization::where('hospital_id', auth()->user()->hospital_id)->get();
        $doctor = Doctor::findOrFail($id);
        $check = $this->canModifyDoctor($doctor);
        if (!$check) {
            abort(403);
        }
        $data = DB::table('doctors as d')->join('users as u', 'u.doctor_id', '=', 'd.id')->where('d.id', '=', $id)->select('d.id', 'u.email', 'd.name', 'd.phone', 'd.gender', 'd.experience_years', 'd.qualification', 'd.specialization_id', 'u.status', 'd.profile_photo')->get()->toArray();
        // pr($data);
        return view('hospital_admin.doctor_form', [
            'title' => 'Update doctor',
            'route' => 'hospital_admin.doctors_update',
            'button' => 'Update',
            'specialization' => $specialization,
            'data' => (array)$data[0]
        ]);
    }
    public function canModifyDoctor(Doctor $doctor): bool
    {
        return $doctor->hospital_id === auth()->user()->hospital_id;
    }
    public function hospital_show($code)
    {
        $hospital = Hospital::where('hospital_code', $code)->first();
        //   pr($hospital->toArray());
        if (!$hospital || empty($hospital)) {
            abort(404);
        }
        //   $specialization=Specialization::select('id','specialization','hospital_id')->where('hospital_id',$hospital->id)->get();
        $doctor = DB::table('doctors as d')
            ->join('specializations as s', 'd.specialization_id', '=', 's.id')->join('users as u','u.doctor_id','=','d.id')->where('u.status',1)
            ->where('d.hospital_id', $hospital->id)
            ->select(
                'd.id',
                'd.name',
                'd.qualification',
                'd.profile_photo',
                'd.experience_years',
                's.specialization',
                's.description'
            )
            ->get();
        return view('users.show_doctors', compact('doctor'));
        //   pr($doctor->toArray());

    }
    public function in_person_form(){
    $hospitalId = auth()->user()->hospital->id;
    $doctors = Doctor::select('id','name')->where('hospital_id',$hospitalId)->get();
        return view('hospital_admin.inperson',compact('doctors'));
    }
        public function ajax_Store(Inpersonrequest $request)
    {
        // dd(Auth::user()->hospital->id);

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
            'hospital_id'   => Auth::user()->hospital->id,
            'doctor_id'     => $request->doctor_id,
            'patient_name'  => $request->patient_name,
            'patient_email' => $request->patient_email??null,
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
        return back()->with('success','booking successfully');
    }
    public function overall_bookings(Request $request)
    {
        // return 'hi';
        $doctorId = auth()->user()->hospital->id;
        $doctors=Doctor::where('hospital_id',$doctorId)->get();
        // pr($doctors->toArray());
        // return $doctorId;
        // Base query with eager loading for optimization
        $query = Booking::where('hospital_id', $doctorId);

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

        return view('hospital_admin.overall-bookings', compact('booking_list','doctors'));
    }
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
            // $doctorId = auth()->user()->hospital_id;
            // if ($booking->doctor_id !== $doctorId) {
            //     return response()->json([
            //         'success' => false,
            //         'msg' => 'Unauthorized: You cannot reschedule this booking'
            //     ], 403);
            // }

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
                'doctor_id' => $request->input('assigned_to')
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
            if ($booking->hospital_id != auth()->user()->hospital_id) {
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
    public function assignDoctor(Request $request, $id) {
    $booking = Booking::findOrFail($id);
    $booking->doctor_id = $request->doctor_id;
    $booking->save();
    return response()->json(['success' => true]);
}

}
