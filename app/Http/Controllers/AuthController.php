<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
   public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:100',
        'email' => 'required|email|max:100|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'phone' => 'required|string|max:20',
        'province_id' => 'required|exists:provinces,id',
        'city_id' => 'required|exists:cities,id',
        'district' => 'nullable|string|max:100',
        'village' => 'nullable|string|max:100',
        'detailed_address' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422);
    }

    $validated = $validator->validated();

    DB::beginTransaction();
    try {
        // Create user location
        $location = UserLocation::create([
            'province_id' => $validated['province_id'],
            'city_id' => $validated['city_id'],
            'district' => $validated['district'] ?? null,
            'village' => $validated['village'] ?? null,
            'detailed_address' => $validated['detailed_address'] ?? null,
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'user_type' => 'peternak',
            'location_id' => $location->id,
            'is_active' => true,
        ]);

        DB::commit();

        // Load relations dengan benar
        $user->load(['location.province', 'location.city']);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Registration error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Registrasi gagal',
            'error' => 'Terjadi kesalahan server. Silakan coba lagi.'
        ], 500);
    }
}
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'error' => 'Email atau password salah'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->update(['last_login' => now()]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'user' => $user->load('location'),
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()->load(['location.province', 'location.city'])
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:100',
            'phone' => 'sometimes|string|max:20',
            'province_id' => 'sometimes|exists:provinces,id',
            'city_id' => 'sometimes|exists:cities,id',
            'district' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'detailed_address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update user
            $user->update($request->only(['name', 'phone']));

            // Update location
            if ($user->location && ($request->province_id || $request->city_id)) {
                $user->location->update([
                    'province_id' => $request->province_id ?? $user->location->province_id,
                    'city_id' => $request->city_id ?? $user->location->city_id,
                    'district' => $request->district,
                    'village' => $request->village,
                    'detailed_address' => $request->detailed_address,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'user' => $user->load(['location.province', 'location.city'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Gagal memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }
}