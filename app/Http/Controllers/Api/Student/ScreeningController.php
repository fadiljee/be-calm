<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\ScreeningAnswer;
use App\Models\ScreeningHistory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ScreeningController extends Controller
{
    use ApiResponse;

    public function submit(Request $request)
    {
        $request->validate([
            'answers'               => 'required|array',
            'answers.*.value'       => 'required|integer',
            'answers.*.question_id' => 'required|integer',
        ]);

        $answers = collect($request->answers);
        $totalScore = $answers->sum('value');
        $jumlahButir = $answers->count();

        // 1. Hitung skor maksimal dinamis berdasarkan jumlah soal
        $skorMaks = $jumlahButir * 4;

        // 2. Hitung persentase (0–100)
        $percentage = $skorMaks > 0 ? round(($totalScore / $skorMaks) * 100, 2) : 0;

        // 3. Tentukan kategori berdasarkan persentase
        if ($percentage <= 25) {
            $conclusion = 'Normal';
        } elseif ($percentage <= 50) {
            $conclusion = 'Stres Ringan';
        } elseif ($percentage <= 75) {
            $conclusion = 'Stres Sedang';
        } else {
            $conclusion = 'Stres Berat';
        }

        // 4. Simpan screening history
        $history = ScreeningHistory::create([
            'student_id'  => $request->user()->id,
            'total_score' => $totalScore,
            'grand_mean'  => $percentage,
            'conclusion'  => $conclusion,
        ]);

        // 5. Simpan tiap jawaban ke tabel screening_answers
        foreach ($request->answers as $ans) {
            ScreeningAnswer::create([
                'screening_history_id' => $history->id,
                'question_id'          => $ans['question_id'],
                'score'                => $ans['value'],
            ]);
        }

        // Sertakan percentage di response agar Flutter bisa langsung pakai
        $responseData = $history->toArray();
        $responseData['percentage'] = $percentage;
        $responseData['max_score']  = $skorMaks;

        return $this->successResponse($responseData, 'Screening submitted successfully', 201);
    }

    public function history(Request $request)
    {
        $history = ScreeningHistory::where('student_id', $request->user()->id)->latest()->get();
        return $this->successResponse($history, 'Screening history retrieved');
    }
}
