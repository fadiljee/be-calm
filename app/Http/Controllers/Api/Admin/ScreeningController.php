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

    public function index(Request $request)
    {
        $category = $request->query('category');
        $perPage  = (int) $request->query('per_page', 20);

        // ── Mode 1: Filter per kategori + pagination ──────────────────────
        if ($category !== null) {
            $categoryMap = [
                'Normal'      => 'Normal',
                'Stres Ringan'=> 'Stres Ringan',
                'Stres Sedang'=> 'Stres Sedang',
                'Stres Berat' => 'Stres Berat',
            ];

            if ($category === 'Belum Tes') {
                $students = User::where('role', 'student')
                    ->whereDoesntHave('screeningHistories')
                    ->paginate($perPage);

                $students->getCollection()->transform(function ($student) {
                    $unreadCount = \App\Models\Message::where('sender_id', $student->id)
                        ->where('receiver_id', auth()->id())
                        ->where('is_read', false)
                        ->count();

                    return [
                        'id'             => $student->id,
                        'name'           => $student->name,
                        'nisn'           => $student->nisn,
                        'kelas'          => $student->kelas,
                        'email'          => $student->email,
                        'unread_count'   => $unreadCount,
                        'has_unread'     => $unreadCount > 0,
                        'last_screening' => null,
                    ];
                });

                return $this->successResponse($students, "Siswa kategori Belum Tes");
            }

            $students = User::where('role', 'student')
                ->whereHas('screeningHistories', function ($q) use ($categoryMap, $category) {
                    $q->where('conclusion', $categoryMap[$category] ?? $category)
                      ->whereIn('id', function ($sub) {
                          $sub->selectRaw('MAX(id)')
                              ->from('screening_histories')
                              ->groupBy('student_id');
                      });
                })
                ->with(['screeningHistories' => function ($q) {
                    $q->latest();
                }])
                ->paginate($perPage);

            // Transform data agar sama persis formatnya dengan Mode 2
            $students->getCollection()->transform(function ($student) {
                $latest = $student->screeningHistories->first();
                $unreadCount = \App\Models\Message::where('sender_id', $student->id)
                    ->where('receiver_id', auth()->id())
                    ->where('is_read', false)
                    ->count();

                return [
                    'id'             => $student->id,
                    'name'           => $student->name,
                    'nisn'           => $student->nisn,
                    'kelas'          => $student->kelas,
                    'email'          => $student->email,
                    'unread_count'   => $unreadCount,
                    'has_unread'     => $unreadCount > 0,
                    'last_screening' => $latest ? [
                        'conclusion'  => $latest->conclusion,
                        'percentage'  => $latest->grand_mean,
                        'total_score' => $latest->total_score,
                        'created_at'  => $latest->created_at,
                    ] : null,
                ];
            });

            return $this->successResponse($students, "Siswa kategori {$category}");
        }

        // ── Mode 2: Summary grouped per kategori ──
        $allStudents = User::where('role', 'student')
            ->with(['screeningHistories' => function ($q) {
                $q->latest();
            }])
            ->get();

        $categories = [
            'Normal'      => [],
            'Stres Ringan'=> [],
            'Stres Sedang'=> [],
            'Stres Berat' => [],
            'Belum Tes'   => [],
        ];

        foreach ($allStudents as $student) {
            $latest = $student->screeningHistories->first();
            $unreadCount = \App\Models\Message::where('sender_id', $student->id)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->count();
                
            $studentData = [
                'id'             => $student->id,
                'name'           => $student->name,
                'nisn'           => $student->nisn,
                'kelas'          => $student->kelas,
                'email'          => $student->email,
                'unread_count'   => $unreadCount,
                'has_unread'     => $unreadCount > 0,
                'last_screening' => $latest ? [
                    'conclusion'  => $latest->conclusion,
                    'percentage'  => $latest->grand_mean,
                    'total_score' => $latest->total_score,
                    'created_at'  => $latest->created_at,
                ] : null,
            ];

            if (!$latest) {
                $categories['Belum Tes'][] = $studentData;
            } elseif (isset($categories[$latest->conclusion])) {
                $categories[$latest->conclusion][] = $studentData;
            } else {
                $categories['Belum Tes'][] = $studentData;
            }
        }

        $grouped = [];
        foreach ($categories as $cat => $students) {
            $grouped[] = [
                'category' => $cat,
                'count'    => count($students),
                'students' => array_slice($students, 0, $perPage),
                'has_more' => count($students) > $perPage,
            ];
        }

        return $this->successResponse([
            'grouped'     => $grouped,
            'total_siswa' => $allStudents->count(),
        ], 'Daftar skor siswa berhasil dimuat');
    }

    public function show($student_id)
    {
        $student = User::where('id', $student_id)->where('role', 'student')->firstOrFail();
        $history = ScreeningHistory::where('student_id', $student_id)->latest()->get()
            ->map(function ($h) {
                return [
                    'id'          => $h->id,
                    'conclusion'  => $h->conclusion,
                    'percentage'  => $h->grand_mean,
                    'total_score' => $h->total_score,
                    'created_at'  => $h->created_at,
                ];
            });

        return $this->successResponse([
            'student' => $student,
            'history' => $history
        ], 'Student history retrieved');
    }

    public function historyDetail($id)
    {
        $history = ScreeningHistory::with(['answers.question'])->findOrFail($id);

        $answers = $history->answers->map(function ($ans) {
            return [
                'id'            => $ans->id,
                'question_id'   => $ans->question_id,
                'score'         => $ans->score,
                'question_text' => $ans->question?->question_text ?? 'Pertanyaan tidak ditemukan',
            ];
        });

        return $this->successResponse([
            'id'          => $history->id,
            'conclusion'  => $history->conclusion,
            'percentage'  => $history->grand_mean,
            'total_score' => $history->total_score,
            'created_at'  => $history->created_at,
            'answers'     => $answers,
        ], 'Detail riwayat berhasil dimuat');
    }

    public function destroy($id)
    {
        $history = ScreeningHistory::findOrFail($id);
        $history->delete();
        return $this->successResponse(null, 'Screening history deleted successfully');
    }
}
