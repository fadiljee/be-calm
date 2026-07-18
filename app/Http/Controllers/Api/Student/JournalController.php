<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Mail\ResetPinJournalMail;
use App\Models\Journal;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class JournalController extends Controller
{
    use ApiResponse;

   public function checkPin(Request $request)
{
    $user = $request->user();
    // Cek log di server apakah $user->journal_pin itu null atau ada isinya
    \Log::info("PIN User: " . $user->journal_pin); 
    
    $hasPin = !is_null($user->journal_pin);
    return $this->successResponse(['has_pin' => $hasPin], 'PIN status retrieved');
}

public function removePin(Request $request)
    {
        $user = $request->user();

        // Kosongkan PIN di database
        $user->journal_pin = null;
        $user->save();

        return $this->successResponse(null, 'PIN Jurnal berhasil dihapus');
    }

    public function setupPin(Request $request)
    {
        // Tambahkan validasi old_pin (opsional/nullable)
        $request->validate([
            'pin' => 'required|digits:4',
            'old_pin' => 'nullable|digits:4'
        ]);

        $user = $request->user();

        // Jika user SUDAH PUNYA PIN
        if (!is_null($user->journal_pin)) {
            // Skenario 1: Ubah PIN biasa (Membawa old_pin dari aplikasi)
            if ($request->filled('old_pin')) {
                if (!Hash::check($request->old_pin, $user->journal_pin)) {
                    return $this->errorResponse('PIN lama tidak sesuai', 400);
                }
            } 
            // Skenario 2: Lupa PIN (Tidak bawa old_pin, wajib sudah verifikasi OTP)
            else if (!$user->pin_reset_verified) {
                return $this->errorResponse('Verifikasi OTP diperlukan untuk mereset PIN', 403);
            }
        }

        // Simpan PIN baru
        $user->journal_pin        = Hash::make($request->pin);
        $user->pin_reset_verified = false; // reset flag setelah digunakan
        $user->save();

        return $this->successResponse(null, 'PIN berhasil ' . (is_null($user->getOriginal('journal_pin')) ? 'dibuat' : 'diperbarui'));
    }

    public function verifyPin(Request $request)
    {
        $request->validate(['pin' => 'required|digits:4']);

        $user = $request->user();

        if (Hash::check($request->pin, $user->journal_pin)) {
            return $this->successResponse(null, 'PIN Valid');
        }

        return $this->errorResponse('PIN Salah', 400);
    }

    public function forgotPin(Request $request)
    {
        $user = $request->user();

        if (!$user->email) {
            return $this->errorResponse('Akun ini tidak memiliki email yang terdaftar', 400);
        }

        $otp = rand(100000, 999999);

        $user->otp_code       = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        Mail::to($user->email)->send(new ResetPinJournalMail($otp));

        return $this->successResponse(null, 'OTP berhasil dikirim ke email');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $user = $request->user();

        if ($user->otp_code == $request->otp && Carbon::now()->isBefore($user->otp_expires_at)) {
            $user->otp_code           = null;
            $user->otp_expires_at     = null;
            $user->pin_reset_verified = true; // tandai bahwa OTP sudah diverifikasi
            $user->save();

            return $this->successResponse(null, 'OTP Valid, silakan buat PIN baru');
        }

        return $this->errorResponse('OTP salah atau sudah kedaluwarsa', 400);
    }

    public function index(Request $request)
    {
        $journals = Journal::where('student_id', $request->user()->id)
                           ->latest()
                           ->get();

        return $this->successResponse($journals, 'Daftar jurnal berhasil diambil');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content'    => 'required|string',
            'mood'       => 'required|string',
            'mood_label' => 'required|string',
            'date'       => 'required|string',
        ]);

        $journal = Journal::create([
            'student_id' => $request->user()->id,
            'content'    => $validated['content'],
            'mood'       => $validated['mood'],
            'mood_label' => $validated['mood_label'],
            'date'       => $validated['date'],
        ]);

        return $this->successResponse($journal, 'Jurnal berhasil disimpan', 201);
    }

    public function show(Request $request, Journal $journal)
    {
        if ($journal->student_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        return $this->successResponse($journal, 'Detail jurnal berhasil diambil');
    }

    public function update(Request $request, Journal $journal)
    {
        if ($journal->student_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'content'    => 'sometimes|required|string',
            'mood'       => 'sometimes|required|string',
            'mood_label' => 'sometimes|required|string',
            'date'       => 'sometimes|required|string',
        ]);

        $journal->update($validated);

        return $this->successResponse($journal, 'Jurnal berhasil diperbarui');
    }

    public function destroy(Request $request, Journal $journal)
    {
        if ($journal->student_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $journal->delete();

        return $this->successResponse(null, 'Jurnal berhasil dihapus');
    }
}