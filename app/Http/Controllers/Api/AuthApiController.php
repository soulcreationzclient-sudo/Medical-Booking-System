<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends BaseApiController
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Auth"},
     *     summary="Get your API token",
     *     description="Login with email and password to receive your API token. Use this token as `Authorization: Bearer {token}` on all protected endpoints.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email",    type="string", example="admin@hospital.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="ABC123XYZ"),
     *             @OA\Property(property="name",  type="string", example="Dr. Smith"),
     *             @OA\Property(property="role",  type="string", example="hospital_admin")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->whereIn('role', ['hospital_admin', 'super_admin', 'doctor'])
            ->where('status', 1)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'token' => $user->api_code,
            'name'  => $user->name,
            'role'  => $user->role,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     tags={"Auth"},
     *     summary="Get current authenticated user info",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Current user",
     *         @OA\JsonContent(
     *             @OA\Property(property="name",        type="string"),
     *             @OA\Property(property="email",       type="string"),
     *             @OA\Property(property="role",        type="string"),
     *             @OA\Property(property="hospital_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function me(Request $request)
    {
        $user = $request->get('_api_user');
        return response()->json([
            'name'        => $user->name,
            'email'       => $user->email,
            'role'        => $user->role,
            'hospital_id' => $user->hospital_id,
        ]);
    }
}