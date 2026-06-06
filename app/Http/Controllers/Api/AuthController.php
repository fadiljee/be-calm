<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);

        $identity = $request->email;

        // Cari berdasarkan email atau NISN
        $user = User::where('email', $identity)
                    ->orWhere('nisn', $identity)
                    ->first();

        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Password salah', 401);
        }

        return $this->respondWithToken($user, 'Login berhasil');
    }

    public function loginOrangTua(Request $request)
    {
        $request->validate([
            'nisn'     => 'required',
            'password' => 'required',
        ]);

        // Cari siswa berdasarkan NISN
        $siswa = User::where('nisn', $request->nisn)
                     ->where('role', 'student')
                     ->first();

        if (!$siswa) {
            return $this->errorResponse('Data anak tidak ditemukan', 404);
        }

        // Verifikasi nama anak (case-insensitive)
        if (strtolower(trim($request->password)) !== strtolower(trim($siswa->name))) {
            return $this->errorResponse('Nama anak tidak sesuai', 401);
        }

        // Cari akun orang tua yang terhubung
        $orangTua = User::where('id', $siswa->parent_id)
                        ->where('role', 'parent')
                        ->first();

        if (!$orangTua) {
            return $this->errorResponse('Akun orang tua belum terdaftar untuk anak ini', 404);
        }

        return $this->respondWithToken($orangTua, 'Login orang tua berhasil');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }

    public function profile(Request $request)
    {
        return $this->successResponse($request->user(), 'Profile data fetched');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return $this->successResponse(null, 'Password berhasil diubah');
    }

    private function respondWithToken(User $user, string $message)
    {
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ], $message);
    }
}