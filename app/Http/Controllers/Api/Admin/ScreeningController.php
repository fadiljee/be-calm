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
            ->with('screeningHistories')
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

        $history = ScreeningHistory::where('student_id', $student_id)
                                   ->latest()
                                   ->get();

        return $this->successResponse([
            'student' => $student,
            'history' => $history,
        ], 'Student history retrieved');
    }
}
