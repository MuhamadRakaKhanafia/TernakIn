<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegistrationForm()
    {
        $provinces = DB::table('province')->orderBy('name')->get();
        return view('auth.register', compact('provinces'));
    }

    public function getCities($province_id)
    {
        $cities = DB::table('city')
                   ->where('province_id', $province_id)
                   ->orderBy('name')
                   ->get();
        
        return response()->json($cities);
    }

    public function register(Request $request)
    {
        // Handle both AJAX and normal form submission
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'province_id' => 'required|exists:province,id',
            'city_id' => 'required|exists:city,id',
            'district' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'detailed_address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            // Jika request AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Jika form submission biasa
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Create user location
            $location = UserLocation::create([
                'province_id' => $request->province_id,
                'city_id' => $request->city_id,
                'district' => $request->district,
                'village' => $request->village,
                'detailed_address' => $request->detailed_address,
            ]);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'user_type' => 'peternak',
                'location_id' => $location->id,
                'is_active' => true,
            ]);

            DB::commit();

            // Login user automatically
            Auth::login($user);

            // Response berdasarkan tipe request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registrasi berhasil!',
                    'redirect_url' => route('welcome')
                ], 201);
            }

            // Redirect untuk form submission biasa
            return redirect()->route('welcome')
                ->with('success', 'Registrasi berhasil! Selamat datang.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Jika request AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registrasi gagal',
                    'error' => 'Terjadi kesalahan server. Silakan coba lagi.'
                ], 500);
            }
            
            // Jika form submission biasa
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan server. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'redirect_url' => route('welcome')
                ]);
            }

            return redirect()->route('welcome')
                ->with('success', 'Login berhasil! Selamat datang kembali.');
        }

        // Jika request AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Email atau password salah.'
            ], 401);
        }
        
        // Jika form submission biasa
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ]);
        }

        return redirect()->route('login')
            ->with('success', 'Logout berhasil');
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load(['location.province', 'location.city']);
        $provinces = DB::table('province')->orderBy('name')->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        }

        return view('auth.profile', compact('user', 'provinces'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:100',
            'phone' => 'sometimes|string|max:20',
            'province_id' => 'sometimes|exists:province,id',
            'city_id' => 'sometimes|exists:city,id',
            'district' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'detailed_address' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            // Check if this is an AJAX request (either JSON or FormData)
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }

                // Store new profile picture
                $profilePicturePath = $request->file('profile_picture')->store('profile-pictures', 'public');
                $user->profile_picture = $profilePicturePath;
                $user->save();
            }

            // Update user
            $user->update($request->only(['name', 'phone']));

            // Update or create location
            if ($request->province_id || $request->city_id || $request->district || $request->village || $request->detailed_address) {
                if ($user->location) {
                    $user->location->update([
                        'province_id' => $request->province_id ?? $user->location->province_id,
                        'city_id' => $request->city_id ?? $user->location->city_id,
                        'district' => $request->district,
                        'village' => $request->village,
                        'detailed_address' => $request->detailed_address,
                    ]);
                } else {
                    $location = UserLocation::create([
                        'province_id' => $request->province_id,
                        'city_id' => $request->city_id,
                        'district' => $request->district,
                        'village' => $request->village,
                        'detailed_address' => $request->detailed_address,
                    ]);
                    $user->location_id = $location->id;
                    $user->save();
                }
            }

            DB::commit();

            // Check if this is an AJAX request (either JSON or FormData)
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'message' => 'Profil berhasil diperbarui',
                    'user' => $user->load(['location.province', 'location.city'])
                ]);
            }

            return redirect()->back()
                ->with('success', 'Profil berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            Log::error('Profile update error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            // Check if this is an AJAX request (either JSON or FormData)
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan. Silakan coba lagi.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }
}