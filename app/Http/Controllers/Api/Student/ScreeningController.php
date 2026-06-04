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
    $totalScore = $answers->sum('value');
    $jumlahButir = $answers->count();
    
    // 1. Hitung Grand Mean
    $grandMean = $totalScore / $jumlahButir;

    // 2. Logika Kesimpulan (Interval sesuai rumus kamu)
    if ($grandMean >= 2.5) {
        $conclusion = 'Baik';
    } elseif ($grandMean >= 1.5) {
        $conclusion = 'Sedang';
    } else {
        $conclusion = 'Risiko Tinggi';
    }

    // 3. Simpan ke Database (Termasuk grand_mean)
    $history = ScreeningHistory::create([
        'student_id'  => $request->user()->id,
        'total_score' => $totalScore,
        'grand_mean'  => $grandMean, // <--- Sekarang tersimpan!
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
