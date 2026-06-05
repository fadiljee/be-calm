<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\ScreeningHistory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ScreeningController extends Controller
{
    use ApiResponse;

    public function submit(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.value' => 'required|integer',
        ]);

        $answers = collect($request->answers);
        
        // 1. Hitung Total Score (Penjumlahan semua nilai jawaban)
        $totalScore = $answers->sum('value');
        
        // Catatan: Jika ingin tetap menyimpan grand mean untuk data analitik, baris ini bisa dipertahankan.
        $jumlahButir = $answers->count();
        $grandMean = $jumlahButir > 0 ? $totalScore / $jumlahButir : 0;

        // 2. Logika Kesimpulan berdasarkan Total Score sesuai foto
        if ($totalScore >= 91 && $totalScore <= 120) {
            $conclusion = 'Stres Berat';
        } elseif ($totalScore >= 61 && $totalScore <= 90) {
            $conclusion = 'Stres Sedang';
        } elseif ($totalScore >= 31 && $totalScore <= 60) {
            $conclusion = 'Stres Ringan';
        } else {
            // Meng-cover rentang 1-30 (atau kondisi jika score di bawah itu)
            $conclusion = 'Normal';
        }

        // 3. Simpan ke Database
        $history = ScreeningHistory::create([
            'student_id'  => $request->user()->id,
            'total_score' => $totalScore,
            'grand_mean'  => $grandMean, 
            'conclusion'  => $conclusion,
        ]);

        return $this->successResponse($history, 'Screening submitted successfully', 201);
    }

    public function history(Request $request)
    {
        $history = ScreeningHistory::where('student_id', $request->user()->id)->latest()->get();
        return $this->successResponse($history, 'Screening history retrieved');
    }
}