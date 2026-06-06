<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationMessage;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $teachers = User::where('role', 'admin')->get([
            'id',
            'name',
            'email',
            'phone',
            'specialization',
            'bio',
        ]);

        return $this->successResponse($teachers, 'Daftar Guru BK berhasil dimuat');
    }

    public function store(Request $request)
    {
        $request->validate([
            'topic'   => 'required',
            'message' => 'required',
        ]);

        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            return $this->errorResponse('Tidak ada Guru BK yang tersedia', 404);
        }

        $consultation = Consultation::create([
            'student_id' => $request->user()->id,
            'admin_id'   => $admin->id,
            'topic'      => $request->topic,
            'status'     => 'open',
        ]);

        ConsultationMessage::create([
            'consultation_id' => $consultation->id,
            'sender_id'       => $request->user()->id,
            'message'         => $request->message,
        ]);

        return $this->successResponse(
            $consultation->load('messages'),
            'Sesi konsultasi berhasil dibuat',
            201
        );
    }

    public function show(Request $request, Consultation $consultation)
    {
        if ($consultation->student_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        return $this->successResponse(
            $consultation->load('messages'),
            'Detail konsultasi berhasil diambil'
        );
    }

    public function sendMessage(Request $request, Consultation $consultation)
    {
        if ($consultation->student_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($consultation->status === 'closed') {
            return $this->errorResponse('Konsultasi sudah ditutup', 400);
        }

        $request->validate([
            'message' => 'required',
        ]);

        $message = ConsultationMessage::create([
            'consultation_id' => $consultation->id,
            'sender_id'       => $request->user()->id,
            'message'         => $request->message,
        ]);

        return $this->successResponse($message, 'Pesan terkirim', 201);
    }
}
