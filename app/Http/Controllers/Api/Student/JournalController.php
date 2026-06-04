<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPinJournalMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
class JournalController extends Controller
{
    use ApiResponse;

    // 1. Cek apakah user sudah punya PIN
    public function checkPin(Request $request)
    {
        $hasPin = !is_null($request->user()->journal_pin);
        return response()->json(['has_pin' => $hasPin], 200);
    }

    // 2. Buat PIN Baru
    public function setupPin(Request $request)
    {
        $request->validate(['pin' => 'required|digits:4']);
        $user = $request->user();
        
        // Simpan dalam bentuk terenkripsi (Bcrypt) agar aman
        $user->journal_pin = Hash::make($request->pin);
        $user->save();

        return response()->json(['message' => 'PIN berhasil dibuat'], 200);
    }

    // 3. Verifikasi PIN saat mau masuk jurnal
    public function verifyPin(Request $request)
    {
        $request->validate(['pin' => 'required|digits:4']);
        $user = $request->user();

        // Cocokkan input dengan hash di database
        if (Hash::check($request->pin, $user->journal_pin)) {
            return response()->json(['message' => 'PIN Valid'], 200);
        }

        return response()->json(['message' => 'PIN Salah'], 400);
    }

  public function forgotPin(Request $request)
    {
        $user = $request->user();

        // Pastikan user punya email
        if (!$user->email) {
            return response()->json(['message' => 'Akun ini tidak memiliki email yang terdaftar.'], 400);
        }

        // Generate 6 digit OTP acak
        $otp = rand(100000, 999999);

        // Simpan ke database (berlaku 15 menit)
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        // Kirim email
        Mail::to($user->email)->send(new ResetPinJournalMail($otp));

        return response()->json(['message' => 'OTP berhasil dikirim ke email'], 200);
    }

    // 2. Verifikasi OTP dari Flutter
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        $user = $request->user();

        // Cek apakah OTP cocok dan belum kedaluwarsa
        if ($user->otp_code == $request->otp && Carbon::now()->isBefore($user->otp_expires_at)) {
            
            // Jika valid, hapus OTP agar tidak bisa dipakai 2x
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            return response()->json(['message' => 'OTP Valid, silakan buat PIN baru'], 200);
        }

        return response()->json(['message' => 'OTP salah atau sudah kedaluwarsa!'], 400);
    }
    public function index(Request $request)
    {
        $journals = Journal::where('student_id', $request->user()->id)
                    ->latest()
                    ->get();
                    
        return $this->successResponse($journals, 'Daftar jurnal berhasil diambil');
    }

    /**
     * Menyimpan jurnal baru dari aplikasi Flutter.
     */
   public function store(Request $request)
{
    // 1. Validasi Input
    // Pastikan key (sebelah kiri) sesuai dengan yang dikirim dari Flutter jsonEncode
    $validated = $request->validate([
        'content'    => 'required|string',
        'mood'       => 'required|string',
        'mood_label' => 'required|string',
        'date'       => 'required|string',
    ]);

    try {
        // 2. Simpan ke Database menggunakan data yang sudah tervalidasi
        $journal = Journal::create([
            'student_id' => $request->user()->id, // Ambil ID dari token sanctum
            'content'    => $validated['content'],
            'mood'       => $validated['mood'],
            'mood_label' => $validated['mood_label'],
            'date'       => $validated['date'],
        ]);

        // 3. Berikan Response Sukses menggunakan Trait ApiResponse kamu
        return $this->successResponse(
            $journal, 
            'Jurnal berhasil disimpan ke database', 
            201
        );

    } catch (\Exception $e) {
        // 4. Tangani jika terjadi error database
        return response()->json([
            'meta' => [
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal menyimpan jurnal: ' . $e->getMessage()
            ]
        ], 500);
    }
}
}