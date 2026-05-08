<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\DailyActivity;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $activities = DailyActivity::where('student_id', $request->user()->id)->latest()->get();
        return $this->successResponse($activities, 'Activities retrieved');
    }

   public function store(Request $request)
{
    // 1. Validasi input sesuai yang dikirim Flutter
    $request->validate([
        'activity_key' => 'required|string',
        'status' => 'required|boolean',
    ]);

    // 2. Gunakan updateOrCreate agar data per-hari per-item tidak duplikat
    // Kita asumsikan satu baris mewakili satu aktivitas per hari
    $activity = DailyActivity::updateOrCreate(
        [
            'student_id' => $request->user()->id,
            'activity_date' => now()->toDateString(), // Ambil tanggal hari ini
            'title' => $request->activity_key,        // Masukkan key sebagai judul
        ],
        [
            'description' => $request->status ? 'Done' : 'Not Done',
            'mood' => 'neutral', // default
        ]
    );

    return $this->successResponse($activity, 'Activity recorded', 201);
}

    public function update(Request $request, DailyActivity $activity)
    {
        if ($activity->student_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $request->validate([
            'activity_date' => 'sometimes|required|date',
            'title' => 'sometimes|required',
            'description' => 'sometimes|required',
            'mood' => 'nullable|in:happy,sad,neutral,angry',
        ]);

        $activity->update($request->all());
        return $this->successResponse($activity, 'Activity updated');
    }

    public function destroy(Request $request, DailyActivity $activity)
    {
        if ($activity->student_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $activity->delete();
        return $this->successResponse(null, 'Activity deleted');
    }
}
