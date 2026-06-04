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

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user = $request->user();
        
        // Enkripsi password baru dan simpan
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diubah'
        ], 200);
    }
private function respondWithToken($user, $message) {
        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], $message);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required', 
            'password' => 'required',
        ]);

        $identity = $request->email;
        $inputPassword = $request->password;

        // Cari berdasarkan email (untuk semua user) atau NISN (untuk siswa)
        $user = User::where('email', $identity)
                    ->orWhere('nisn', $identity)
                    ->first();

        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        // Verifikasi password menggunakan Hash::check yang standar
        if (!Hash::check($inputPassword, $user->password)) {
            return $this->errorResponse('Password salah', 401);
        }

        return $this->respondWithToken($user, 'Login berhasil');
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
    public function loginOrangTua(Request $request)
{
    $request->validate([
        'nisn' => 'required',
        'password' => 'required', // Ini diisi nama anak
    ]);

    // 1. Cari siswa berdasarkan NISN
    $siswa = User::where('nisn', $request->nisn)
                 ->where('role', 'student') // Sesuaikan dengan enum 'student' di DB-mu
                 ->first();

    if (!$siswa) {
        return $this->errorResponse('Data anak tidak ditemukan', 404);
    }

    // 2. Verifikasi nama anak (case-insensitive)
    if (strtolower(trim($request->password)) !== strtolower(trim($siswa->name))) {
        return $this->errorResponse('Nama anak tidak sesuai', 401);
    }

    // 3. Cari akun orang tua yang terhubung dengan anak ini
    $orangTua = User::where('id', $siswa->parent_id)
                    ->where('role', 'parent')
                    ->first();

    if (!$orangTua) {
        return $this->errorResponse('Akun orang tua belum terdaftar untuk anak ini', 404);
    }

    // 4. Buat token untuk orang tua
    $token = $orangTua->createToken('auth_token')->plainTextToken;

    return $this->successResponse([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $orangTua,
    ], 'Login orang tua berhasil');
}
}