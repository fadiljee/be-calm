<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\ScreeningHistory;
use App\Models\ScreeningAnswer; // <-- Jangan lupa import Model baru
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ScreeningController extends Controller
{
    use ApiResponse;

    public function submit(Request $request)
    {
        // ── 1. UPDATE VALIDASI ──
        // Pastikan Flutter juga mengirimkan 'question_id' untuk setiap jawaban
        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.value' => 'required|integer',
        ]);

        $answers = collect($request->answers);
        
        // Hitung Total Score (Penjumlahan semua nilai jawaban)
        $totalScore = $answers->sum('value');
        
        $jumlahButir = $answers->count();
        $grandMean = $jumlahButir > 0 ? $totalScore / $jumlahButir : 0;

        // Logika Kesimpulan berdasarkan Total Score
        if ($totalScore >= 91 && $totalScore <= 120) {
            $conclusion = 'Stres Berat';
        } elseif ($totalScore >= 61 && $totalScore <= 90) {
            $conclusion = 'Stres Sedang';
        } elseif ($totalScore >= 31 && $totalScore <= 60) {
            $conclusion = 'Stres Ringan';
        } else {
            $conclusion = 'Normal';
        }

        // Simpan ke Database Induk (History)
        $history = ScreeningHistory::create([
            'student_id'  => $request->user()->id,
            'total_score' => $totalScore,
            'grand_mean'  => $grandMean, 
            'conclusion'  => $conclusion,
        ]);

        // ── 2. TAMBAHAN: SIMPAN RINCIAN JAWABAN KE TABEL BARU ──
        $detailAnswers = [];
        foreach ($request->answers as $ans) {
            $detailAnswers[] = [
                'screening_history_id' => $history->id,
                'question_id'          => $ans['question_id'],
                'score'                => $ans['value'], // Mengambil dari 'value'
                'created_at'           => now(),
                'updated_at'           => now(),
            ];
        }
        
        // Gunakan insert agar proses penyimpanan ke database lebih cepat (sekali query)
        ScreeningAnswer::insert($detailAnswers);

        return $this->successResponse($history->load('answers.question'), 'Screening submitted successfully', 201);
    }

    public function history(Request $request)
    {
        // Tambahkan with('answers.question') jika siswa juga butuh melihat rinciannya
        $history = ScreeningHistory::where('student_id', $request->user()->id)
                                   ->with('answers.question')
                                   ->latest()
                                   ->get();
                                   
        return $this->successResponse($history, 'Screening history retrieved');
    }
}