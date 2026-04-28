<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inpersonrequest;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\HospitalFinancial;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PatientBillingEntry;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\BookingTreatment;
use App\Models\Treatment;
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
    // ══════════════════════════════════════════════════════════
    //  DASHBOARD
    // ══════════════════════════════════════════════════════════

    public function index()
    {
        return view('home');
    }

    // ══════════════════════════════════════════════════════════
    //  DOCTORS
    // ══════════════════════════════════════════════════════════

    public function add_doctor_view()
    {
        $doctors = DB::table('doctors as d')
            ->where('d.hospital_id', auth()->user()->hospital_id)
            ->select('d.id', 'd.name', 'd.profile_photo', 'd.doctor_code', 'd.qualification', 'd.phone', 'd.specialization_id')
            ->get();

        // Load all specializations from pivot for each doctor
        foreach ($doctors as $doctor) {
            try {
                $specs = DB::table('doctor_specializations as ds')
                    ->join('specializations as s', 's.id', '=', 'ds.specialization_id')
                    ->where('ds.doctor_id', $doctor->id)
                    ->pluck('s.specialization')
                    ->toArray();
            } catch (\Exception $e) {
                $specs = [];
            }

            // Fallback to specialization_id column if pivot is empty
            if (empty($specs) && $doctor->specialization_id) {
                $spec  = DB::table('specializations')->where('id', $doctor->specialization_id)->value('specialization');
                $specs = $spec ? [$spec] : [];
            }

            $doctor->specialization = implode(', ', $specs);
        }

        return view('hospital_admin.hospital_admin_dashboard', compact('doctors'));
    }

    public function doctor_form()
    {
        $specialization = Specialization::where('hospital_id', auth()->user()->hospital_id)->get();
        return view('hospital_admin.doctor_form', [
            'route'                   => 'hospital_admin.doctor_add',
            'title'                   => 'Create doctor',
            'button'                  => 'Submit',
            'specialization'          => $specialization,
            'doctorSpecializationIds' => [],
        ]);
    }

    public function doctor_add(Request $request)
    {
        $id      = $request->id;
        $user_id = null;

        if ($id) {
            $user_id = User::where('doctor_id', $id)
                ->where('hospital_id', auth()->user()->hospital_id)
                ->value('id');
        }

        $rules = [
            'name'             => 'required|string|max:255',
            'email'            => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user_id),
            ],
            'phone'            => 'required|string|max:20',
            'gender'           => 'required',
            'experience_years' => 'required|numeric',
            'specialization'   => 'required|array|min:1',
            'qualification'    => 'required|string',
            'status'           => 'nullable',
            'profile_photo'    => 'nullable|image|max:2048',
            'consultation_fee' => 'nullable|numeric|min:0',
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
                $photo    = $request->file('profile_photo');
                $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                Storage::disk('s3')->putFileAs('doctors', $photo, $filename);
                $doctor->profile_photo = 'doctors/' . $filename;
            }

            $specializationIds = is_array($validated['specialization'])
                ? array_map('intval', $validated['specialization'])
                : [(int) $validated['specialization']];

            $doctor->fill([
                'name'              => $validated['name'],
                'hospital_id'       => auth()->user()->hospital_id,
                'gender'            => $validated['gender'],
                'doctor_code'       => $id ? $doctor->doctor_code : $this->doctorcode(),
                'experience_years'  => $validated['experience_years'],
                'phone'             => $validated['phone'],
                'specialization_id' => $specializationIds[0],
                'qualification'     => $validated['qualification'],
                'consultation_fee'  => $validated['consultation_fee'] ?? 0,
            ]);

            $doctor->save();

            // ── Sync multiple specializations ──────────────────────
            try {
                $doctor->specializations()->sync($specializationIds);
            } catch (\Exception $e) {
                // doctor_specializations table may not exist yet — run php artisan migrate
                Log::channel('hospital_admin')->warning('Specialization sync skipped', ['error' => $e->getMessage()]);
            }

            $user = User::where('doctor_id', $doctor->id)
                ->where('hospital_id', auth()->user()->hospital_id)
                ->first();

            if (!$user) {
                $user            = new User();
                $user->doctor_id = $doctor->id;
                $user->api_code  = $this->uniquecode('user');
            }

            $user->name        = $validated['name'];
            $user->email       = $validated['email'];
            $user->role        = 'doctor';
            $user->hospital_id = auth()->user()->hospital_id;
            $user->status      = $request->has('status') ? 1 : 0;

            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }

            $user->save();

            DB::commit();

            Log::channel('hospital_admin')->info(
                $id ? 'Doctor updated successfully' : 'Doctor created successfully',
                ['doctor_id' => $doctor->id, 'user_id' => $user->id]
            );

            return back()->with(
                'success',
                $id ? 'Doctor updated successfully' : 'Doctor created successfully'
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::channel('hospital_admin')->error('Doctor save failed', [
                'error'     => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
                'doctor_id' => $id,
            ]);
            return back()->with('error', 'Something went wrong while saving doctor details. Please try again.');
        }
    }

    public function doctor_delete(Request $request)
    {
        if (!$request->id) {
            return response()->json(['success' => false, 'message' => 'Missing doctor ID'], 422);
        }

        $doctor = Doctor::find($request->id);

        if (!$doctor) {
            return response()->json(['success' => false, 'message' => 'Doctor not found'], 404);
        }

        if (!$this->canModifyDoctor($doctor)) {
            return response()->json(['success' => false, 'message' => 'Authorization error'], 403);
        }

        @Storage::disk('s3')->delete($doctor->profile_photo);
        $doctor->delete();

        return response()->json(['success' => true, 'message' => 'Doctor deleted successfully']);
    }

    public function edit_doctor_view($id)
    {
        $specialization = Specialization::where('hospital_id', auth()->user()->hospital_id)->get();
        $doctor         = Doctor::findOrFail($id);
        $check          = $this->canModifyDoctor($doctor);

        if (!$check) {
            abort(403);
        }

        $data = DB::table('doctors as d')
            ->join('users as u', 'u.doctor_id', '=', 'd.id')
            ->where('d.id', '=', $id)
            ->select(
                'd.id', 'u.email', 'd.name', 'd.phone', 'd.gender',
                'd.experience_years', 'd.qualification', 'd.specialization_id',
                'u.status', 'd.profile_photo', 'd.consultation_fee'
            )
            ->get()
            ->toArray();

        $doctorSpecializationIds = DB::table('doctor_specializations')
            ->where('doctor_id', $id)
            ->pluck('specialization_id')
            ->toArray();

        // Fallback: if pivot is empty, use specialization_id column
        if (empty($doctorSpecializationIds) && isset($data[0]->specialization_id)) {
            $doctorSpecializationIds = [$data[0]->specialization_id];
        }

        return view('hospital_admin.doctor_form', [
            'title'                    => 'Update doctor',
            'route'                    => 'hospital_admin.doctors_update',
            'button'                   => 'Update',
            'specialization'           => $specialization,
            'data'                     => (array) $data[0],
            'doctorSpecializationIds'  => $doctorSpecializationIds,
        ]);
    }

    public function canModifyDoctor(Doctor $doctor): bool
    {
        return $doctor->hospital_id === auth()->user()->hospital_id;
    }

    // ══════════════════════════════════════════════════════════
    //  SPECIALIZATION
    // ══════════════════════════════════════════════════════════

    public function specialization_view()
    {
        $specialization = Specialization::where('hospital_id', auth()->user()->hospital->id)->get();
        return view('hospital_admin.specialization', compact('specialization'));
    }

    public function specialization_add(Request $request)
    {
        if (empty($request->specialization)) {
            return back()->with('error', 'Must enter specialization type');
        }

        $id = auth()->user()->hospital?->id;
        if (!$id) {
            return back()->with('error', 'Hospital not found');
        }

        Specialization::create([
            'hospital_id'    => $id,
            'specialization' => $request->specialization,
            'description'    => $request->description ?? null,
        ]);

        return back()->with('success', 'Specialization added');
    }

    public function specialization_delete(Request $request)
    {
        if (!$request->id) {
            return response()->json(['success' => false, 'message' => 'Missing specialization ID'], 422);
        }

        $specialization = Specialization::find($request->id);

        if (!$specialization) {
            return response()->json(['success' => false, 'message' => 'Specialization not found'], 404);
        }

        $check = $this->updatedelete_check($request->id);
        if (!$check) {
            return response()->json(['success' => false, 'msg' => 'Authorization error'], 403);
        }

        if ($specialization->delete()) {
            return response()->json(['success' => true, 'message' => 'Specialization deleted']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete specialization'], 500);
    }

    public function specialization_edit(Request $request)
    {
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
            }
            $specialization->update([
                'specialization' => $request->specialization,
                'description'    => $request->description,
            ]);
            return back()->with('success', 'Specialization updated');
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

    // ══════════════════════════════════════════════════════════
    //  HOSPITAL PUBLIC PAGE
    // ══════════════════════════════════════════════════════════

    public function hospital_show($code)
    {
        $hospital = Hospital::where('hospital_code', $code)->first();

        if (!$hospital || empty($hospital)) {
            abort(404);
        }

        $doctor = DB::table('doctors as d')
            ->join('specializations as s', 'd.specialization_id', '=', 's.id')
            ->join('users as u', 'u.doctor_id', '=', 'd.id')
            ->where('u.status', 1)
            ->where('d.hospital_id', $hospital->id)
            ->select(
                'd.id', 'd.name', 'd.qualification', 'd.profile_photo',
                'd.experience_years', 's.specialization', 's.description'
            )
            ->get();

        return view('users.show_doctors', compact('doctor'));
    }

    // ══════════════════════════════════════════════════════════
    //  IN-PERSON BOOKINGS
    // ══════════════════════════════════════════════════════════

    public function in_person_form()
    {
        $hospitalId = auth()->user()->hospital->id;
        $doctors    = Doctor::select('id', 'name')->where('hospital_id', $hospitalId)->get();
        return view('hospital_admin.inperson', compact('doctors'));
    }

    public function ajax_Store(Inpersonrequest $request)
    {
        do {
            $actionToken = 'BK-' . strtoupper(Str::random(8));
        } while (DB::table('bookings')->where('action_token', $actionToken)->exists());

        DB::table('patients')->updateOrInsert(
            ['phone_no' => $request->patient_phone],
            [
                'name'                   => $request->patient_name,
                'age'                    => $request->age                    ?? null,
                'gender'                 => $request->gender                 ?? null,
                'ic_passport_no'         => $request->ic_passport_no         ?? null,
                'dob'                    => $request->dob                    ?? null,
                'blood_type'             => $request->blood_type             ?? null,
                'marital_status'         => $request->marital_status         ?? null,
                'nationality'            => $request->nationality             ?? null,
                'address'                => $request->address                ?? null,
                'state'                  => $request->state                  ?? null,
                'city'                   => $request->city                   ?? null,
                'postcode'               => $request->postcode               ?? null,
                'country'               => $request->country                ?? null,
                'emergency_contact_name' => $request->emergency_contact_name ?? null,
                'emergency_contact_no'   => $request->emergency_contact_no   ?? null,
                'updated_at'             => now(),
                'created_at'             => now(),
            ]
        );

        DB::table('bookings')->insert([
            'hospital_id'   => Auth::user()->hospital->id,
            'doctor_id'     => $request->doctor_id,
            'patient_name'  => $request->patient_name,
            'patient_email' => $request->patient_email ?? null,
            'patient_phone' => $request->patient_phone,
            'age'           => $request->age ?? null,
            'cause'         => $request->cause ?? null,
            'booking_date'  => $request->booking_date,
            'start_time'    => $request->start_time,
            'end_time'      => null,
            'status'        => 'pending',
            'action_token'  => $actionToken,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
        $this->createSpeedbotsContact($request->patient_phone, Auth::user()->hospital->id, $request->booking_date, $request->start_time, $actionToken);
        return back()->with('success', 'Booking created successfully');
    }

    // ══════════════════════════════════════════════════════════
    //  OVERALL BOOKINGS
    // ══════════════════════════════════════════════════════════

    public function overall_bookings(Request $request)
    {
        $doctorId = auth()->user()->hospital->id;
        $doctors  = Doctor::where('hospital_id', $doctorId)->get();
        $query    = Booking::where('hospital_id', $doctorId);

        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

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
                $query->where(function ($q) {
                    $q->where('status', 'pending')
                        ->orWhereDate('booking_date', today());
                });
                break;
        }

        $booking_list = $query->latest()->get();

        $stats = [
            'total' => Booking::where('hospital_id', $doctorId)->count(),
            'pending' => Booking::where('hospital_id', $doctorId)->where('status', 'pending')->count(),
            'accepted' => Booking::where('hospital_id', $doctorId)->where('status', 'accepted')->count(),
            'completed' => Booking::where('hospital_id', $doctorId)->where('status', 'completed')->count(),
            'rescheduled' => Booking::where('hospital_id', $doctorId)->where('status', 'rescheduled')->count(),
            'rejected_no_show' => Booking::where('hospital_id', $doctorId)
                ->whereIn('status', ['rejected', 'no_show'])
                ->count(),
        ];

        return view('hospital_admin.overall-bookings', compact('booking_list', 'doctors', 'stats'));
    }

    public function reschedule(Request $request, $id)
    {
        $validated = $request->validate([
            'new_date' => ['required', 'date', 'after_or_equal:today'],
            'new_time' => ['required', 'date_format:H:i'],
            'reason'   => ['nullable', 'string', 'max:500'],
        ], [
            'new_date.required'       => 'New appointment date is required',
            'new_date.after_or_equal' => 'New date must be today or a future date',
            'new_time.required'       => 'New appointment time is required',
            'new_time.date_format'    => 'Time must be in HH:MM format (e.g., 14:30)',
            'reason.max'              => 'Reason must not exceed 500 characters',
        ]);

        DB::beginTransaction();
        try {
            $booking = Booking::findOrFail($id);

            if ($booking->status === 'completed') {
                return response()->json(['success' => false, 'msg' => 'Cannot reschedule a completed booking'], 400);
            }

            $oldDate   = $booking->booking_date;
            $oldTime   = $booking->start_time;
            $oldStatus = $booking->status;

            $booking->booking_date   = $validated['new_date'];
            $booking->start_time     = $validated['new_time'];
            $booking->status         = 'rescheduled';
            $booking->rescheduled_at = now();
            $booking->rescheduled_by = auth()->id();

            if (!empty($validated['reason'])) {
                $booking->reschedule_reason = $validated['reason'];
            }

            $booking->save();

            $this->sendSpeedbotsNotification($booking, $oldStatus, 'rescheduled', [
                'old_date' => $oldDate,
                'old_time' => $oldTime,
                'new_date' => $validated['new_date'],
                'new_time' => $validated['new_time'],
                'reason'   => $validated['reason'] ?? null,
            ]);

            DB::commit();

            Log::channel('doctor')->info('Booking rescheduled successfully', [
                'booking_id' => $booking->id,
                'old_date'   => $oldDate,
                'old_time'   => $oldTime,
                'new_date'   => $validated['new_date'],
                'new_time'   => $validated['new_time'],
            ]);

            return response()->json([
                'success' => true,
                'msg'     => 'Appointment rescheduled successfully',
                'data'    => [
                    'old_date' => $oldDate,
                    'old_time' => $oldTime,
                    'new_date' => $booking->booking_date,
                    'new_time' => $booking->start_time,
                    'reason'   => $validated['reason'] ?? null,
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => 'Booking not found'], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => 'Validation failed', 'errors' => $e->errors()], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reschedule failed', ['booking_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to reschedule appointment. Please try again.'], 500);
        }
    }

    public function update_status(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => [
                'required', 'string',
                Rule::in(['unverified', 'pending', 'accepted', 'rejected', 'cancelled', 'no_show', 'completed'])
            ]
        ]);

        try {
            $booking = Booking::findOrFail($id);

            if ($booking->hospital_id != auth()->user()->hospital_id) {
                return response()->json(['success' => false, 'msg' => 'Unauthorized: You cannot modify this booking'], 403);
            }

            $oldStatus       = $booking->status;
            $booking->status = $validated['status'];

            if ($validated['status'] === 'completed') {
                $booking->completed_at = now();

                // Auto-add consultation fee billing entry to patient profile
                $patient = Patient::where('phone_no', $booking->patient_phone)->first();
                $doctor  = Doctor::find($booking->doctor_id);

                if ($patient && $doctor && $doctor->consultation_fee > 0) {
                    PatientBillingEntry::create([
                        'patient_id'   => $patient->id,
                        'hospital_id'  => $booking->hospital_id,
                        'booking_id'   => $booking->id,
                        'type'         => 'consultation',
                        'description'  => 'Consultation with Dr. ' . $doctor->name,
                        'amount'       => $doctor->consultation_fee,
                        'is_past_note' => false,
                        'is_paid'      => false,
                    ]);
                }
            }

            $booking->save();

            $this->sendSpeedbotsNotification($booking, $oldStatus, $validated['status']);

            Log::channel('doctor')->info('Booking status updated', [
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
            ]);

            return response()->json([
                'success' => true,
                'msg'     => 'Booking status updated successfully',
                'data'    => ['old_status' => $oldStatus, 'new_status' => $validated['status']]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'msg' => 'Booking not found'], 404);

        } catch (\Exception $e) {
            Log::error('Update status failed', ['booking_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'msg' => 'Failed to update status. Please try again.'], 500);
        }
    }

    public function assignDoctor(Request $request, $id)
    {
        $booking            = Booking::findOrFail($id);
        $booking->doctor_id = $request->doctor_id;
        $booking->save();
        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════
    //  MEDICINES
    // ══════════════════════════════════════════════════════════

    public function medicines_index()
    {
        $hospitalId = auth()->user()->hospital_id;
        $medicines  = Medicine::where('hospital_id', $hospitalId)->orderBy('name')->get();

        $stockData = $medicines->map(fn($m) => [
            'name'  => $m->name,
            'stock' => $m->stock,
        ])->values();

        return view('hospital_admin.medicines', compact('medicines', 'stockData'));
    }

    public function medicine_store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'unit'  => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        Medicine::create([
            'hospital_id' => auth()->user()->hospital_id,
            'name'        => $request->name,
            'unit'        => $request->unit,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Medicine added successfully.');
    }

    public function medicine_update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'unit'  => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $medicine = Medicine::where('id', $id)
            ->where('hospital_id', auth()->user()->hospital_id)
            ->firstOrFail();

        $medicine->update($request->only('name', 'unit', 'price', 'stock', 'description'));

        return back()->with('success', 'Medicine updated.');
    }

    public function medicine_delete($id)
    {
        Medicine::where('id', $id)
            ->where('hospital_id', auth()->user()->hospital_id)
            ->firstOrFail()
            ->delete();

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════
    //  TREATMENTS
    // ══════════════════════════════════════════════════════════

    public function treatments_index()
    {
        $hospitalId = auth()->user()->hospital_id;

        $treatments = Treatment::where('hospital_id', $hospitalId)
            ->orderBy('name')
            ->get();

        return view('hospital_admin.treatments', compact('treatments'));
    }
    
    public function treatment_store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'nullable|string|max:100',
            'category'   => 'required|in:consultation,treatment,operation,medicine,other',
            'base_price' => 'required|numeric|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        Treatment::create([
            'hospital_id' => auth()->user()->hospital_id,
            'name'        => $request->name,
            'code'        => $request->code,
            'category'    => $request->category,
            'base_price'  => $request->base_price,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Treatment added successfully.');
}

    public function treatment_update(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'nullable|string|max:100',
            'category'   => 'required|in:consultation,treatment,operation,medicine,other',
            'base_price' => 'required|numeric|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $treatment = Treatment::where('id', $id)
            ->where('hospital_id', auth()->user()->hospital_id)
            ->firstOrFail();

        $treatment->update([
            'name'       => $request->name,
            'code'       => $request->code,
            'category'   => $request->category,
            'base_price' => $request->base_price,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Treatment updated successfully.');
    }

    public function treatment_delete($id)
    {
        $treatment = Treatment::where('id', $id)
            ->where('hospital_id', auth()->user()->hospital_id)
            ->firstOrFail();

        $treatment->delete();

        return response()->json(['success' => true]);
    }

    public function getTreatmentPrice($id)
    {
        $treatment = Treatment::where('id', $id)
            ->where('hospital_id', auth()->user()->hospital_id)
            ->firstOrFail();

        return response()->json([
            'id'         => $treatment->id,
            'name'       => $treatment->name,
            'category'   => $treatment->category,
            'base_price' => (float) $treatment->base_price,
        ]);
    }

    // ══════════════════════════════════════════════════════════
    //  PATIENTS
    // ══════════════════════════════════════════════════════════

    public function patients_index()
    {
        $hospitalId = auth()->user()->hospital_id;

        $patients = DB::table('patients as p')
            ->join('bookings as b', 'b.patient_phone', '=', 'p.phone_no')
            ->where('b.hospital_id', $hospitalId)
            ->select('p.*')
            ->distinct()
            ->orderBy('p.name')
            ->get();

        return view('hospital_admin.patients', compact('patients'));
    }

    public function patient_profile($id)
    {
        $hospitalId = auth()->user()->hospital_id;
        $patient    = Patient::findOrFail($id);

        // ── Billing entries ──────────────────────────────────
        $billingEntries = PatientBillingEntry::with(['booking', 'treatment'])
            ->where('patient_id', $id)
            ->where('hospital_id', $hospitalId)
            ->orderByDesc('created_at')
            ->get();

        // ── Prescriptions (new system) ───────────────────────
        try {
            $prescriptions = Prescription::with('items.medicine')
                ->where('patient_id', $id)
                ->where('hospital_id', $hospitalId)
                ->orderByDesc('created_at')
                ->get();
        } catch (\Exception $e) {
            $prescriptions = collect();
        }

        // ── Medicines for prescription dropdown ──────────────
        $medicines = Medicine::where('hospital_id', $hospitalId)
            ->orderBy('name')
            ->get();

        //── Treatments ──────────────
        $treatments = Treatment::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // ── Booking history with doctor name ─────────────────
        $bookings = DB::table('bookings as b')
            ->leftJoin('doctors as d', 'd.id', '=', 'b.doctor_id')
            ->where('b.patient_phone', $patient->phone_no)
            ->where('b.hospital_id', $hospitalId)
            ->select(
                'b.id',
                'b.action_token',
                'b.booking_date',
                'b.start_time',
                'b.status',
                'b.cause',
                'b.doctor_id',
                'd.name as doctor_name'
            )
            ->latest('b.created_at')
            ->get();

        // ── Financial totals ─────────────────────────────────
        $totalDue  = $billingEntries->where('is_paid', false)->where('is_past_note', false)->sum('amount');
        $totalPaid = $billingEntries->where('is_paid', true)->sum('amount');

        return view('hospital_admin.patient_show', compact(
            'patient',
            'billingEntries',
            'prescriptions',
            'medicines',
            'treatments',
            'bookings',
            'totalDue',
            'totalPaid'
        ));
    }

    public function patient_add_billing(Request $request, $id)
    {
        $request->validate([
            'type'         => 'required|in:consultation,medicine,treatment,operation,custom_profit,custom_expense',
            'description'  => 'nullable|string|max:500',
            'amount'       => 'required|numeric|min:0',
            'is_past_note' => 'nullable|boolean',
            'booking_id'      => 'nullable|exists:bookings,id',
            'treatment_id'    => 'nullable|exists:treatments,id',
            'quantity'        => 'nullable|integer|min:1',
            'unit_price'      => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string|max:255',
        ]);

        $hospitalId = auth()->user()->hospital_id;
        $patient    = Patient::findOrFail($id);

        DB::beginTransaction();

        try {
            $treatment = null;

            if ($request->filled('treatment_id')) {
                $treatment = Treatment::where('id', $request->treatment_id)
                    ->where('hospital_id', $hospitalId)
                    ->firstOrFail();
            }

            if ($request->filled('booking_id')) {
                $booking = Booking::where('id', $request->booking_id)
                    ->where('hospital_id', $hospitalId)
                    ->firstOrFail();

                if ($booking->patient_phone !== $patient->phone_no) {
                    abort(422, 'Selected booking does not belong to this patient.');
                }
            }

            $description = $request->description;
            if (!$description && $treatment) {
                $description = $treatment->name;
            }

            $billingEntry = PatientBillingEntry::create([
                'patient_id'   => $id,
                'hospital_id'  => $hospitalId,
                'booking_id'   => $request->booking_id ?: null,
                'treatment_id' => $request->treatment_id ?: null,
                'type'         => $request->type,
                'description'  => $description,
                'amount'       => $request->amount,
                'is_past_note' => $request->boolean('is_past_note'),
                'is_paid'      => false,
            ]);

            if (
                in_array($request->type, ['treatment', 'operation'], true) &&
                $request->filled('booking_id') &&
                $request->filled('treatment_id')
            ) {
                $quantity       = (int) ($request->quantity ?? 1);
                $unitPrice      = (float) ($request->unit_price ?? ($treatment?->base_price ?? 0));
                $discountAmount = (float) ($request->discount_amount ?? 0);
                $totalAmount    = max(0, ($quantity * $unitPrice) - $discountAmount);

                BookingTreatment::updateOrCreate(
                    [
                        'booking_id'   => $request->booking_id,
                        'treatment_id' => $request->treatment_id,
                    ],
                    [
                        'quantity'        => $quantity,
                        'unit_price'      => $unitPrice,
                        'discount_amount' => $discountAmount,
                        'total_amount'    => $totalAmount,
                        'notes'           => $request->notes,
                    ]
                );

                if (!$request->boolean('is_past_note') && (float) $request->amount !== $totalAmount) {
                    $billingEntry->update([
                        'amount' => $totalAmount,
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Billing entry added.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('patient_add_billing failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Failed to add billing entry.');
        }

        // Mirror income entries into hospital financial ledger
        // if (in_array($request->type, ['consultation', 'treatment', 'operation', 'custom_profit'])) {
        //     HospitalFinancial::create([
        //         'hospital_id' => $hospitalId,
        //         'type'        => 'profit',
        //         'description' => "Patient #{$id}: " . $request->description,
        //         'amount'      => $request->amount,
        //         'entry_date'  => now()->toDateString(),
        //         'created_by'  => auth()->id(),
        //     ]);
        // }

        
    }

    public function patient_mark_paid(Request $request, $id, $entryId)
{
    $entry = PatientBillingEntry::where('id', $entryId)
        ->where('patient_id', $id)
        ->where('hospital_id', auth()->user()->hospital_id)
        ->firstOrFail();
    
    if ($entry->is_paid) {
        return response()->json([
            'success' => true,
            'message' => 'Entry already marked as paid.',
        ]);
    }

    $entry->update([
        'is_paid' => true,
        'paid_at' => now(),
    ]);

    // ── Auto-add to Financial Ledger as Income ──────────────
    $incomTypes = ['consultation', 'medicine', 'treatment', 'operation'];

    if (in_array($entry->type, $incomTypes, true)) {
        HospitalFinancial::create([
            'hospital_id' => auth()->user()->hospital_id,
            'type'        => 'profit',
            'description' => "Patient #{$id}: " . $entry->description,
            'amount'      => $entry->amount,
            'entry_date'  => now()->toDateString(),
            'created_by'  => auth()->id(),
        ]);
    }

    return response()->json(['success' => true, 'message' => 'Marked as paid.']);
}

    public function patient_billing_delete($id, $entryId)
    {
        PatientBillingEntry::where('id', $entryId)
            ->where('patient_id', $id)
            ->where('hospital_id', auth()->user()->hospital_id)
            ->firstOrFail()
            ->delete();

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════
    //  PRESCRIPTIONS
    // ══════════════════════════════════════════════════════════

    public function prescription_create($bookingId)
    {
        $hospitalId = auth()->user()->hospital_id;
        $booking    = Booking::where('id', $bookingId)
            ->where('hospital_id', $hospitalId)
            ->firstOrFail();

        $medicines = Medicine::where('hospital_id', $hospitalId)->orderBy('name')->get();

        return view('hospital_admin.prescription_form', compact('booking', 'medicines'));
    }

    public function prescription_store(Request $request, $bookingId)
    {
        $request->validate([
            'medicines'               => 'required|array|min:1',
            'medicines.*.medicine_id' => 'required|exists:medicines,id',
            'medicines.*.quantity'    => 'required|integer|min:1',
            'medicines.*.dosage'      => 'nullable|string|max:255',
            'notes'                   => 'nullable|string',
        ]);

        $hospitalId = auth()->user()->hospital_id;
        $booking    = Booking::where('id', $bookingId)
            ->where('hospital_id', $hospitalId)
            ->firstOrFail();

        $patient = Patient::where('phone_no', $booking->patient_phone)->firstOrFail();

        DB::beginTransaction();
        try {
            $prescription = Prescription::create([
                'booking_id'  => $booking->id,
                'hospital_id' => $hospitalId,
                'doctor_id'   => $booking->doctor_id,
                'patient_id'  => $patient->id,
                'notes'       => $request->notes,
            ]);

            $totalMedicineCost = 0;

            foreach ($request->medicines as $item) {
                $medicine = Medicine::where('id', $item['medicine_id'])
                    ->where('hospital_id', $hospitalId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($medicine->stock < $item['quantity']) {
                    DB::rollBack();
                    return back()->with('error', "Insufficient stock for {$medicine->name}. Available: {$medicine->stock}");
                }

                $medicine->decrement('stock', $item['quantity']);

                $linePrice          = $medicine->price * $item['quantity'];
                $totalMedicineCost += $linePrice;

                PrescriptionItem::create([
                    'prescription_id'     => $prescription->id,
                    'medicine_id'         => $medicine->id,
                    'quantity'            => $item['quantity'],
                    'price_at_time'       => $medicine->price,
                    'dosage_instructions' => $item['dosage'] ?? null,
                ]);
            }

            // Add medicine cost to patient billing
            if ($totalMedicineCost > 0) {
                PatientBillingEntry::create([
                    'patient_id'   => $patient->id,
                    'hospital_id'  => $hospitalId,
                    'booking_id'   => $booking->id,
                    'type'         => 'medicine',
                    'description'  => 'Prescription #' . $prescription->id . ' — medicines',
                    'amount'       => $totalMedicineCost,
                    'is_past_note' => false,
                    'is_paid'      => false,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('hospital_admin.patients.profile', $patient->id)
                ->with('success', 'Prescription saved and stock updated.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Prescription save failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to save prescription. Please try again.');
        }
    }

    // ══════════════════════════════════════════════════════════
    //  HOSPITAL FINANCIALS
    // ══════════════════════════════════════════════════════════

    public function financials_index()
    {
        $hospitalId = auth()->user()->hospital_id;

        $entries = HospitalFinancial::where('hospital_id', $hospitalId)
            ->orderByDesc('entry_date')
            ->orderByDesc('created_at')
            ->get();

        $totalProfit  = $entries->where('type', 'profit')->sum('amount');
        $totalExpense = $entries->where('type', 'expense')->sum('amount');
        $netBalance   = $totalProfit - $totalExpense;

        $monthlyData = HospitalFinancial::where('hospital_id', $hospitalId)
            ->where('entry_date', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(entry_date, '%Y-%m') as month, type, SUM(amount) as total")
            ->groupBy('month', 'type')
            ->orderBy('month')
            ->get();

        return view('hospital_admin.financials', compact(
            'entries', 'totalProfit', 'totalExpense', 'netBalance', 'monthlyData'
        ));
    }

    public function financial_store(Request $request)
    {
        $request->validate([
            'type'        => 'required|in:profit,expense',
            'description' => 'required|string|max:500',
            'amount'      => 'required|numeric|min:0.01',
            'entry_date'  => 'required|date',
        ]);

        HospitalFinancial::create([
            'hospital_id' => auth()->user()->hospital_id,
            'type'        => $request->type,
            'description' => $request->description,
            'amount'      => $request->amount,
            'entry_date'  => $request->entry_date,
            'created_by'  => auth()->id(),
        ]);

        return back()->with('success', ucfirst($request->type) . ' entry added.');
    }

    public function financial_delete($id)
    {
        HospitalFinancial::where('id', $id)
            ->where('hospital_id', auth()->user()->hospital_id)
            ->firstOrFail()
            ->delete();

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════
    //  SPEEDBOTS NOTIFICATION (private)
    // ══════════════════════════════════════════════════════════

    private function sendSpeedbotsNotification(Booking $booking, string $oldStatus, string $newStatus, array $additionalData = [])
    {
        // Only send WhatsApp notification for accepted / rejected / rescheduled
        if (!in_array($newStatus, ['accepted', 'rejected', 'rescheduled'])) {
            return;
        }

        try {
            if (!$booking->hospital_id) {
                Log::channel('doctor')->warning('Speedbots notification skipped: No hospital_id', ['booking_id' => $booking->id]);
                return;
            }

            $hospital = Hospital::find($booking->hospital_id);

            if (!$hospital) {
                Log::channel('doctor')->warning('Speedbots notification skipped: Hospital not found', ['booking_id' => $booking->id]);
                return;
            }

            // ── Resolve flow ID from hospital settings (DB) ──────────
            // OLD hardcoded fallbacks kept as comments:
            // 'accepted'    => '1774503294935'
            // 'rejected'    => '1774503355823'
            // 'rescheduled' => '1774503413964'
            $flowMap = [
                'accepted'    => $hospital->accept_flow_id,
                'rejected'    => $hospital->reject_flow_id,
                'rescheduled' => $hospital->reschedule_flow_id,
            ];

            $flowId = $flowMap[$newStatus] ?? null;

            if (empty($flowId)) {
                Log::channel('doctor')->warning('Speedbots notification skipped: Missing flow ID for status', [
                    'booking_id' => $booking->id,
                    'status'     => $newStatus,
                ]);
                return;
            }

            if (empty($hospital->token)) {
                Log::channel('doctor')->warning('Speedbots notification skipped: Missing API token', ['booking_id' => $booking->id]);
                return;
            }

            // OLD: used a single hospital->flow_id for all statuses
            // if (empty($hospital->flow_id)) {
            //     Log::channel('doctor')->warning('Speedbots notification skipped: Missing flow_id', ['booking_id' => $booking->id]);
            //     return;
            // }

            if (empty($booking->patient_phone)) {
                Log::channel('doctor')->warning('Speedbots notification skipped: Missing patient phone', ['booking_id' => $booking->id]);
                return;
            }

            $contactId = preg_replace('/[^0-9+]/', '', $booking->patient_phone);

            if (empty($contactId)) {
                Log::channel('doctor')->warning('Speedbots notification skipped: Invalid phone format', ['booking_id' => $booking->id]);
                return;
            }

            Log::channel('doctor')->info('Sending Speedbots notification', [
                'booking_id' => $booking->id,
                'contact_id' => $contactId,
                'flow_id'    => $flowId,  // OLD: $hospital->flow_id
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);

            // ── For reschedule: update date+time custom field with new_date + new_time ──
            if ($newStatus === 'rescheduled') {
                // Use datetime_field_id from hospital settings (DB), OLD hardcoded: 244056
                $dateFieldId = $hospital->datetime_field_id ?? '244056';

                $newDate       = $additionalData['new_date'] ?? $booking->booking_date;
                $newTime       = $additionalData['new_time'] ?? $booking->start_time;
                $dateTimeValue = $newDate . ' ' . $newTime;

                Http::timeout(10)
                    ->withoutVerifying() // SSL bypass for local dev (cURL error 60)
                    ->withHeaders([
                        'X-ACCESS-TOKEN' => $hospital->token,
                        'Content-Type'   => 'application/x-www-form-urlencoded',
                        'accept'         => 'application/json',
                    ])
                    ->asForm()
                    ->post("https://app.speedbots.io/api/contacts/{$contactId}/custom_fields/{$dateFieldId}", [
                        'value' => $dateTimeValue,
                    ]);

                Log::channel('doctor')->info('Speedbots reschedule date+time set', [
                    'contact_id'   => $contactId,
                    'field_id'     => $dateFieldId,
                    'value'        => $dateTimeValue,
                ]);
            }

            // OLD: Update date custom field (244056) — sent for all statuses, now only for rescheduled above
            // Http::timeout(10)
            //     ->withHeaders([
            //         'X-ACCESS-TOKEN' => $hospital->token,
            //         'Content-Type'   => 'application/x-www-form-urlencoded'
            //     ])
            //     ->asForm()
            //     ->post("https://app.speedbots.io/api/contacts/{$contactId}/custom_fields/244056", [
            //         'value' => $booking->booking_date
            //     ]);

            // OLD: Update patient name custom field (947818)
            // $response = Http::timeout(10)
            //     ->withHeaders([
            //         'X-ACCESS-TOKEN' => $hospital->token,
            //         'Content-Type'   => 'application/x-www-form-urlencoded'
            //     ])
            //     ->asForm()
            //     ->post("https://app.speedbots.io/api/contacts/{$contactId}/custom_fields/947818", [
            //         'value' => $booking->patient_name
            //     ]);

            // OLD: Update status custom field (591719)
            // Http::timeout(10)
            //     ->withHeaders([
            //         'X-ACCESS-TOKEN' => $hospital->token,
            //         'Content-Type'   => 'application/x-www-form-urlencoded'
            //     ])
            //     ->asForm()
            //     ->post("https://app.speedbots.io/api/contacts/{$contactId}/custom_fields/591719", [
            //         'value' => $newStatus
            //     ]);

            // ── Send the correct flow based on status ────────
            $response = Http::timeout(10)
                ->withoutVerifying() // SSL bypass for local dev (cURL error 60)
                ->withHeaders([
                    'X-ACCESS-TOKEN' => $hospital->token,
                    'accept'         => 'application/json',
                ])
                ->post("https://app.speedbots.io/api/contacts/{$contactId}/send/{$flowId}");

            if ($response->successful()) {
                Log::channel('doctor')->info('Speedbots notification sent successfully', [
                    'booking_id'  => $booking->id,
                    'flow_id'     => $flowId,
                    'status_code' => $response->status(),
                ]);
            } else {
                Log::channel('doctor')->error('Speedbots notification failed', [
                    'booking_id'  => $booking->id,
                    'flow_id'     => $flowId,
                    'status_code' => $response->status(),
                    'error'       => $response->body(),
                ]);
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::channel('doctor')->error('Speedbots connection error', [
                'booking_id' => $booking->id,
                'message'    => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            Log::channel('doctor')->error('Speedbots notification exception', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
    private function createSpeedbotsContact(string $phone, int $hospitalId, ?string $bookingDate = null, ?string $bookingTime = null, ?string $bookingCode = null): void
    {
        try {
            $hospital = \App\Models\Hospital::find($hospitalId);

            if (!$hospital || empty($hospital->token)) {
                Log::channel('hospital_admin')->warning('Speedbots contact skipped: no token', [
                    'hospital_id' => $hospitalId,
                ]);
                return;
            }

            $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
            if (empty($cleanPhone)) return;

            $token = $hospital->token;

            // ── CALL 1: Create contact (ignore if already exists) ──────
            try {
                Http::timeout(10)
                    ->withoutVerifying()
                    ->withHeaders([
                        'X-ACCESS-TOKEN' => $token,
                        'Content-Type'   => 'application/json',
                        'accept'         => 'application/json',
                    ])
                    ->post('https://app.speedbots.io/api/contacts', [
                        'phone' => $cleanPhone,
                    ]);
            } catch (\Exception $e) {
                // Contact may already exist — continue to set custom fields
                Log::channel('hospital_admin')->info('Speedbots contact create skipped (may exist)', [
                    'phone' => $cleanPhone, 'error' => $e->getMessage(),
                ]);
            }





            // ── CALL 2: Set appointment date custom field ────────
            try {
                $dateFieldId = $hospital->appointment_date_field_id ?? null;
            } catch (\Exception $e) {
                $dateFieldId = null;
            }

            if ($dateFieldId && $bookingDate) {
                Http::timeout(10)
                    ->withoutVerifying()
                    ->withHeaders([
                        'X-ACCESS-TOKEN' => $token,
                        'Content-Type'   => 'application/x-www-form-urlencoded',
                        'accept'         => 'application/json',
                    ])
                    ->asForm()
                    ->post("https://app.speedbots.io/api/contacts/{$cleanPhone}/custom_fields/{$dateFieldId}", [
                        'value' => $bookingDate,
                    ]);

                Log::channel('hospital_admin')->info('Speedbots date field set', [
                    'phone'    => $cleanPhone,
                    'field_id' => $dateFieldId,
                    'value'    => $bookingDate,
                ]);
            }

            // ── CALL 3: Set appointment time custom field ────────
            try {
                $timeFieldId = $hospital->appointment_time_field_id ?? null;
            } catch (\Exception $e) {
                $timeFieldId = null;
            }

            if ($timeFieldId && $bookingTime) {
                try {
                    $formattedTime = \Carbon\Carbon::createFromFormat('H:i:s', $bookingTime)->format('h:i A');
                } catch (\Exception $e) {
                    $formattedTime = $bookingTime;
                }

                Http::timeout(10)
                    ->withoutVerifying()
                    ->withHeaders([
                        'X-ACCESS-TOKEN' => $token,
                        'Content-Type'   => 'application/x-www-form-urlencoded',
                        'accept'         => 'application/json',
                    ])
                    ->asForm()
                    ->post("https://app.speedbots.io/api/contacts/{$cleanPhone}/custom_fields/{$timeFieldId}", [
                        'value' => $formattedTime,
                    ]);

                Log::channel('hospital_admin')->info('Speedbots time field set', [
                    'phone'    => $cleanPhone,
                    'field_id' => $timeFieldId,
                    'value'    => $formattedTime,
                ]);
            }

            // ── CALL 4: Set booking code custom field ───────────
            try {
                $bookingCodeFieldId = $hospital->booking_code_field_id ?? null;
            } catch (\Exception $e) {
                $bookingCodeFieldId = null;
            }

            if ($bookingCodeFieldId && $bookingCode) {
                Http::timeout(10)
                    ->withoutVerifying()
                    ->withHeaders([
                        'X-ACCESS-TOKEN' => $token,
                        'Content-Type'   => 'application/x-www-form-urlencoded',
                        'accept'         => 'application/json',
                    ])
                    ->asForm()
                    ->post("https://app.speedbots.io/api/contacts/{$cleanPhone}/custom_fields/{$bookingCodeFieldId}", [
                        'value' => $bookingCode,
                    ]);

                Log::channel('hospital_admin')->info('Speedbots booking code field set', [
                    'phone'    => $cleanPhone,
                    'field_id' => $bookingCodeFieldId,
                    'value'    => $bookingCode,
                ]);
            }

        } catch (\Exception $e) {
            Log::channel('hospital_admin')->error('Speedbots contact failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function calendar(Request $request)
{
    $hospitalId = auth()->user()->hospital_id;
 
    // Determine which month to show
    $currentMonth = $request->filled('month')
        ? \Carbon\Carbon::parse($request->month . '-01')
        : now()->startOfMonth();
 
    $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
    $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');
 
    // Fetch ALL bookings for this month (± overflow days)
    $startDate = $currentMonth->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
    $endDate   = $currentMonth->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
 
    $bookings = DB::table('bookings as b')
        ->leftJoin('doctors as d', 'd.id', '=', 'b.doctor_id')
        ->where('b.hospital_id', $hospitalId)
        ->whereBetween('b.booking_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->select(
            'b.id', 'b.patient_name', 'b.patient_phone',
            'b.booking_date', 'b.start_time', 'b.status',
            'b.cause', 'b.action_token',
            'd.name as doctor_name'
        )
        ->orderBy('b.start_time')
        ->get();
 
    // Group bookings by date string for fast lookup
    $bookingsByDate = $bookings->groupBy('booking_date');
 
    // Build weeks array for the blade template
    $weeks = [];
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
 
    // Pass flat list for JS day-overflow modal
    $allBookings = $bookings->map(fn($b) => [
        'id'           => $b->id,
        'patient_name' => $b->patient_name,
        'patient_phone'=> $b->patient_phone,
        'booking_date' => $b->booking_date,
        'start_time'   => $b->start_time,
        'status'       => $b->status,
        'cause'        => $b->cause,
        'action_token' => $b->action_token,
        'doctor_name'  => $b->doctor_name,
    ])->values();
 
    return view('hospital_admin.calendar', compact(
        'weeks', 'currentMonth', 'prevMonth', 'nextMonth', 'allBookings'
    ));
}

    // ══════════════════════════════════════════════════════════
    //  SPEEDBOTS SETTINGS (hospital admin)
    // ══════════════════════════════════════════════════════════

    public function speedbots_settings()
    {
        $hospital = Hospital::find(auth()->user()->hospital_id);

        if (!$hospital) {
            abort(404);
        }

        return view('hospital_admin.speedbots_settings', compact('hospital'));
    }

    public function speedbots_settings_update(Request $request)
    {
        $hospital = Hospital::find(auth()->user()->hospital_id);

        if (!$hospital) {
            abort(404);
        }

        $request->validate([
            'token'                    => 'nullable|string|max:255',
            'accept_flow_id'           => 'nullable|string|max:50',
            'reject_flow_id'           => 'nullable|string|max:50',
            'reschedule_flow_id'       => 'nullable|string|max:50',
            'datetime_field_id'        => 'nullable|string|max:50',
            'appointment_date_field_id'=> 'nullable|string|max:100',
            'appointment_time_field_id'=> 'nullable|string|max:100',
            'booking_code_field_id'    => 'nullable|string|max:100',
        ]);

        $hospital->update([
            'token'                     => $request->token,
            'accept_flow_id'            => $request->accept_flow_id,
            'reject_flow_id'            => $request->reject_flow_id,
            'reschedule_flow_id'        => $request->reschedule_flow_id,
            'datetime_field_id'         => $request->datetime_field_id,
            'appointment_date_field_id' => $request->appointment_date_field_id,
            'appointment_time_field_id' => $request->appointment_time_field_id,
            'booking_code_field_id'     => $request->booking_code_field_id,
        ]);

        Log::channel('hospital_admin')->info('Speedbots settings updated', [
            'hospital_id' => $hospital->id,
            'user_id'     => auth()->id(),
        ]);

        return back()->with('success', 'Speedbots settings saved successfully.');
    }

}
//ok