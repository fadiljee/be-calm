<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScreeningHistory;
use App\Models\User;
use App\Traits\ApiResponse;

class ScreeningController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $students = User::where('role', 'student')
            // ── UPDATE EAGER LOADING DI SINI ──
            // Memanggil relasi answers dan memanggil relasi question di dalam answers
            ->with(['screeningHistories' => function ($query) {
                $query->latest(); // Mengurutkan dari yang terbaru
            }, 'screeningHistories.answers.question']) 
            ->withCount(['sentMessages as unread_count' => function ($query) {
                $query->where('receiver_id', auth()->id())
                      ->where('is_read', false);
            }])
            ->latest()
            ->get();

        return $this->successResponse($students, 'Student scores retrieved successfully');
    }

    public function show($student_id)
    {
        $student = User::where('id', $student_id)
                       ->where('role', 'student')
                       ->firstOrFail();

        // ── UPDATE EAGER LOADING ──
        $history = ScreeningHistory::where('student_id', $student_id)
                                   ->with('answers.question')
                                   ->latest()
                                   ->get();

        return $this->successResponse([
            'student' => $student,
            'history' => $history,
        ], 'Student history retrieved');
    }

    public function destroy($id) {
    $history = ScreeningHistory::findOrFail($id);
    $history->delete(); // Ini otomatis akan menghapus data di screening_answers jika di database sudah diset ON DELETE CASCADE
    return response()->json(['message' => 'Deleted']);
}
}