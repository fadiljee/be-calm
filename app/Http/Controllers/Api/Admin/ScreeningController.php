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
    // Ambil data siswa + history screening + jumlah pesan yang masuk ke Guru BK tapi belum dibaca
    $students = User::where('role', 'student')
        ->with('screeningHistories')
        ->withCount(['sentMessages as unread_count' => function ($query) {
            // Asumsinya: mencari pesan yang dikirim siswa ini KE guru yang sedang login
            // dan status pesannya belum dibaca (is_read = false)
            $query->where('receiver_id', auth()->id())
                  ->where('is_read', false); 
        }])
        ->latest()
        ->get();

    return $this->successResponse($students, 'Student scores retrieved successfully');
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
