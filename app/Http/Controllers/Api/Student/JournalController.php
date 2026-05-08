<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class JournalController extends Controller
{
    use ApiResponse;

    /**
     * Menampilkan daftar jurnal milik siswa yang sedang login.
     */
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