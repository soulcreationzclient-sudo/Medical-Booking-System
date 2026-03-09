<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class Superadmincontroller extends Controller
{
    //
    public function index()
    {
        // Auth::logout();
        return view('home');
    }
    public function add_hospital_view()
    {
        Cache::forget('hospital_admin.datatable');
        $hospital_admin = Cache::remember(
            'hospital_admin.datatable',
            600,
            function () {
                return Hospital::select('id', 'hospital_name', 'city', 'hospital_phone', 'admin_phone')
                    ->orderBy('id')
                    ->limit(500)   // 🔴 IMPORTANT: keep a safe limit
                    ->get();
            }
        );

        return view('super_admin.add_hospital_view', [
            'hospitals' => $hospital_admin
        ]);
    }

    public function add_hospital_form()
    {
        $fields = [
            'email' => ['type' => 'text', 'label' => 'Email'],
            'password' => ['type' => 'password', 'label' => 'Password'],
            'hospital_name' => ['type' => 'text', 'label' => 'Hospital name'],
            'flow_id'=>['type'=>'text','label'=>'Flow Id'],
            'hospital_phone' => ['type' => 'tel', 'label' => 'Hospital phone'],
            'admin_name' => ['type' => 'text'],
            'admin_phone' => ['type' => 'tel', 'label' => 'Admin phone'],
            'address_line' => ['type' => 'textarea', 'col' => 'col-md-12'],
            'address_line2' => ['type' => 'textarea', 'col' => 'col-md-12'],
            'city' => ['type' => 'text'],
            'country' => ['type' => 'text'],
            'db_status' => [
                'type' => 'radio',
                'options' => [0 => 'Testing', 1 => 'Production'],
                'col' => 'col-md-12'
            ],
            'is_active' => ['type' => 'checkbox', 'label' => 'Active'],
            'hospital_logo' => ['type' => 'file', 'label' => 'Logo image']
        ];
        // pr($fields);
        return view('super_admin.add_hospital_form', [
            'fields' => $fields,
            'action' => route('super_admin.hospital_add'),
            'method' => 'POST',
            'model' => null,
            'submit' => 'Create Hospital',
        ]);
    }
    public function add_hospital(Request $request)
    {
        $hospital_id = $request->input('id');

        // Load hospital once
        $hospital = $hospital_id ? Hospital::find($hospital_id) : null;
        if (!$hospital_id) {
            $user = User::where('email', $request->email)->exists();
            if ($user) {
                return back()->with('error', 'Mail is already use');
            }
        }
        // Load user once
        $user = $hospital ? User::where('hospital_id', $hospital->id)->first() : null;
        $user_id = $user?->id;

        // Prevent role misuse
        if ($request->email) {
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser && $existingUser->role !== 'hospital_admin') {
                return back()->with('error', 'You are only allowed to create or modify a hospital admin.');
            }
        }

        // ✅ VALIDATION
        $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user_id),
            ],

            'password' => $user_id ? 'nullable|min:6' : 'required|min:6',

            'hospital_name' => 'required|string',
            'hospital_phone' => 'required|string',
            'admin_name' => 'required|string',
            'admin_phone' => 'required|string',
            'address_line' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'db_status' => 'required',
            'flow_id'=>'nullable|string',

            'hospital_logo' => $hospital_id
                ? 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048'
                : 'required|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::transaction(function () use ($request, $hospital, $user) {

            // ✅ HANDLE LOGO
            $logoPath = $hospital?->hospital_logo; // existing logo path (if any)

            if ($request->hasFile('hospital_logo')) {

                // 1️⃣ Ensure directory exists
                $dir = public_path('hospital_logos');
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                // 2️⃣ Delete old logo (if exists)
                if (!empty($logoPath)) {
                    $oldPath = public_path($logoPath);
                    if (Storage::disk('s3')->exists($logoPath)) {
                        Storage::disk('s3')->delete($logoPath);
                    }
                }
                // 3️⃣ Prepare new filename (time-based)
                $file = $request->file('hospital_logo');
                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

                // 4️⃣ Move file
                Storage::disk('s3')->putFileAs(
                    'hospital_logos',   // folder
                    $file,              // uploaded file
                    $filename           // filename
                );

                // 5️⃣ Save relative path (DB-safe)
                $logoPath = 'hospital_logos/' . $filename;
            }

            // ✅ USER
            if ($user) {
                $user->update([
                    'email' => $request->email,
                    'name' => $request->admin_name,
                    'password' => $request->password
                        ? Hash::make($request->password)
                        : $user->password,
                    'status' => $request->is_active ?? 0,
                ]);
            } else {
                $user = User::create([
                    'email' => $request->email,
                    'name' => $request->admin_name,
                    'password' => Hash::make($request->password),
                    'api_code' => $this->uniquecode('user'),
                    'status' => $request->is_active ?? 0,
                    'role' => 'hospital_admin',
                ]);
            }

            // ✅ HOSPITAL
            if ($hospital) {
                $hospital->update([
                    'hospital_name' => $request->hospital_name,
                    'hospital_phone' => $request->hospital_phone,
                    'admin_name' => $request->admin_name,
                    'admin_phone' => $request->admin_phone,
                    'address_line' => $request->address_line,
                    'address_line2' => $request->address_line2,
                    'city' => $request->city,
                    'country' => $request->country,
                    'db_status' => $request->db_status,
                    'hospital_logo' => $logoPath,
                    'flow_id'=>$request->flow_id
                ]);
            } else {
                $hospital = Hospital::create([
                    'hospital_name' => $request->hospital_name,
                    'hospital_code' => $this->uniquecode('user'),
                    'hospital_phone' => $request->hospital_phone,
                    // 'hospital_logo' => $logo,
                    'admin_name' => $request->admin_name,
                    'admin_phone' => $request->admin_phone,
                    'address_line' => $request->address_line,
                    'address_line2' => $request->address_line2,
                    'city' => $request->city,
                    'country' => $request->country,
                    'db_status' => $request->db_status,
                    'hospital_logo' => $logoPath,
                    'flow_id'=>$request->flow_id
                ]);
            }

            // ✅ LINK
            $user->update([
                'hospital_id' => $hospital->id
            ]);
        });
        Cache::forget('hospital_admin');
        return back()->with('success', 'Hospital saved successfully');
    }

    public function edit_hospital_view($id)
    {
        Cache::forget('hospital_admin.datatable');
        // 2️⃣ Fetch hospital + admin user data
        $data = DB::table('hospitals as h')
            ->join('users as u', 'u.hospital_id', '=', 'h.id')->where('role','hospital_admin')
            ->where('h.id', $id)
            ->select(
                'h.id as id',
                'u.email as email',
                'h.hospital_name as hospital_name',
                'h.hospital_phone as hospital_phone',
                'h.admin_name as admin_name',
                'h.admin_phone as admin_phone',
                'h.address_line as address_line',
                'h.address_line2 as address_line2',
                'h.city as city',
                'h.country as country',
                'h.db_status as db_status',
                'u.status as is_active',
                'h.hospital_logo as hospital_logo',
                'h.flow_id as flow_id'
            )
            ->first();
        // pr((array)$data);
        // 4️⃣ Return view
        return view('super_admin.edit_hospital_view', [
            'data' => (array)$data,
            'action' => route('super_admin.hospital_add', $id),
            'method' => 'POST',
            'model' => null,
            'submit' => 'Update Hospital',
        ]);
    }


    public function edit_hospital(Request $request, $id)
    {
        Log::channel('super_admin')->info('Edit hospital started', [
            'hospital_id' => $id,
            'user_id' => auth()->id()
        ]);

        $hospital_check = Hospital::findOrFail($id);
// return 'hi';
        $user_check = User::where('hospital_id', $hospital_check->id)
            ->where('role', 'hospital_admin')
            ->first();

        $user_id = $user_check?->id;

        $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user_id),
            ],
            'hospital_name'  => 'required|string',
            'hospital_phone' => 'required|string',
            'admin_name'     => 'required|string',
            'admin_phone'    => 'required|string',
            'address_line'   => 'required|string',
            'city'           => 'required|string',
            'country'        => 'required|string',
            'db_status'      => 'required|in:0,1',
            'password'       => 'nullable|min:6',
            'flow_id'   =>'nullable',
            'hospital_logo'  => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::beginTransaction();

        // try {

            $hospital = Hospital::findOrFail($id);

            $user = User::where('hospital_id', $hospital->id)
                ->where('role', 'hospital_admin')
                ->firstOrFail();

            $userData = [
                'email'       => $request->email,
                'name'        => $request->admin_name,
                'status'      => $request->is_active ?? 0,
                'hospital_id' => $hospital->id,
                'role'        => 'hospital_admin',
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            $hospitalData = [
                'hospital_name'  => $request->hospital_name,
                'hospital_phone' => $request->hospital_phone,
                'admin_name'     => $request->admin_name,
                'admin_phone'    => $request->admin_phone,
                'address_line'   => $request->address_line,
                'address_line2'  => $request->address_line2,
                'city'           => $request->city,
                'country'        => $request->country,
                'db_status'      => $request->db_status,
                'flow_id'=>$request->flow_id
            ];

            if ($request->hasFile('hospital_logo')) {

                if ($hospital->hospital_logo) {
                    Storage::disk('s3')->delete($hospital->hospital_logo);
                }

                $file = $request->file('hospital_logo');
                $filename = time() . '_' . $file->getClientOriginalName();

                Storage::disk('s3')->putFileAs('hospital_logos', $file, $filename);

                $hospitalData['hospital_logo'] = 'hospital_logos/' . $filename;
            }

            $hospital->update($hospitalData);

            DB::commit();

            Cache::forget('hospital_admin');

            Log::channel('super_admin')->info('Edit hospital completed successfully', [
                'hospital_id' => $hospital->id,
                'user_id' => $user->id
            ]);

            return redirect()
                ->back()
                ->with('success', 'Hospital updated successfully');
        // } catch (\Throwable $e) {

            DB::rollBack();

            Log::channel('super_admin')->error('Edit hospital failed', [
                'hospital_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Something went wrong. Please try again.');
        // }
    }


    public function delete_hospital(Request $request)
    {
        if (!$request->id) {
            return response()->json([
                'success' => false,
                'message' => 'Missing hospital ID'
            ], 422);
        }

        $hospital = Hospital::find($request->id);

        if (!$hospital) {
            return response()->json([
                'success' => false,
                'message' => 'Hospital not found'
            ], 404);
        }

        User::where('hospital_id', $hospital->id)
            ->where('role', 'hospital_admin')
            ->delete();
        @Storage::disk('s3')->delete($hospital->hospital_logo);
        User::where('hospital_id', $request->id)
            ->where('role', 'doctor')
            ->update(['status' => false]);
        $hospital->delete();
        return response()->json([
            'success' => true,
            'message' => 'Hospital and hospital admins deleted successfully'
        ]);
    }
    public function test()
    {
        return response()->json([
            'msg' => 'Deleted',
            'success' => true
        ], 200);
    }
    public function delete_s3()
    {
        $path = Storage::disk('s3')->files('doctors');
        Storage::disk('s3')->delete($path);
        $k = Storage::disk('s3')->files('hospital_logos');
        Storage::disk('s3')->delete($k);
        return response()->json($path);
    }
}
