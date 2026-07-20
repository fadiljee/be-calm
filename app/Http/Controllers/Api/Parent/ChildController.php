<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use App\Models\ScreeningHistory;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    use ApiResponse;

    public function children(Request $request)
    {
        // Eager load riwayat screening terbaru setiap anak sekaligus
        // agar dashboard orang tua tidak perlu N+1 request
        $children = User::where('parent_id', $request->user()->id)
            ->with(['screeningHistories' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->get()
            ->map(function ($child) {
                $latest = $child->screeningHistories->first();
                return [
                    'id'             => $child->id,
                    'name'           => $child->name,
                    'nisn'           => $child->nisn,
                    'kelas'          => $child->kelas,
                    'email'          => $child->email,
                    'last_screening' => $latest ? [
                        'conclusion'  => $latest->conclusion,
                        'percentage'  => $latest->grand_mean, // grand_mean menyimpan persentase
                        'total_score' => $latest->total_score,
                        'created_at'  => $latest->created_at,
                    ] : null,
                ];
            });

        return $this->successResponse($children, 'Daftar anak berhasil dimuat');
    }

    public function childScores(Request $request, $student_id)
    {
        // Validasi apakah benar ini anak dari orang tua yang login
        User::where('id', $student_id)
            ->where('parent_id', $request->user()->id)
            ->firstOrFail();
            
        // Ambil semua riwayat screening anak
        $scores = ScreeningHistory::where('student_id', $student_id)->latest()->get()
            ->map(function ($h) {
                return [
                    'id'          => $h->id,
                    'conclusion'  => $h->conclusion,
                    'percentage'  => $h->grand_mean, // grand_mean menyimpan persentase
                    'total_score' => $h->total_score,
                    'created_at'  => $h->created_at,
                ];
            });
        
        return $this->successResponse($scores, 'Riwayat screening anak berhasil dimuat');
    }
}

