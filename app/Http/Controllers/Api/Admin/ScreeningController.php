<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScreeningHistory;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ScreeningController extends Controller
{
    use ApiResponse;

// app/Http/Controllers/Api/Admin/ScreeningController.php

    public function index()
    {
        // Ambil semua user dengan role student
        // dan muat relasi screeningHistories miliknya
        $students = User::where('role', 'student')
            ->with(['screeningHistories' => function($query) {
                $query->latest(); // Urutkan riwayat dari yang terbaru
            }])
            ->get();

        return $this->successResponse($students, 'Daftar skor siswa berhasil dimuat');
    }

    public function show($student_id)
    {
        $student = User::where('id', $student_id)->where('role', 'student')->firstOrFail();
        $history = ScreeningHistory::where('student_id', $student_id)->latest()->get();
        
        return $this->successResponse([
            'student' => $student,
            'history' => $history
        ], 'Student history retrieved');
    }
}
