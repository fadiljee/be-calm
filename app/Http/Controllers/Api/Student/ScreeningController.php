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
    // 1. Validasi bahwa ada list jawaban
    $request->validate([
        'answers' => 'required|array',
        'answers.*.value' => 'required|integer',
    ]);

    // 2. Hitung total skor dari array answers
    $totalScore = collect($request->answers)->sum('value');

    // 3. Tentukan Kesimpulan (Sesuai logika kamu)
    $conclusion = 'Normal';
    if ($totalScore > 20) {
        $conclusion = 'Butuh Konseling';
    } elseif ($totalScore > 10) {
        $conclusion = 'Rentan';
    }

    // 4. Simpan ke Database
    $history = ScreeningHistory::create([
        'student_id' => $request->user()->id,
        'total_score' => $totalScore,
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
