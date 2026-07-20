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

    public function store(Request $request)
    {
        $request->validate([
            'topic' => 'required',
            'message' => 'required',
        ]);

        // Find an admin (Guru BK) - for simplicity take the first one or a specific one if needed
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            return $this->errorResponse('No Admin (Guru BK) available', 404);
        }

        $consultation = Consultation::create([
            'student_id' => $request->user()->id,
            'admin_id' => $admin->id,
            'topic' => $request->topic,
            'status' => 'open',
        ]);

        ConsultationMessage::create([
            'consultation_id' => $consultation->id,
            'sender_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        return $this->successResponse($consultation->load('messages'), 'Consultation session created', 201);
    }

    public function show(Request $request, Consultation $consultation)
    {
        if ($consultation->student_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        return $this->successResponse($consultation->load('messages'), 'Consultation details retrieved');
    }
    public function index()
    {
        // Filter user dengan role 'admin'
        $teachers = User::where('role', 'admin')->get([
            'id', 
            'name', 
            'email', 
            'phone', 
            'specialization', 
            'bio'
        ])->map(function ($teacher) {
            $unreadCount = \App\Models\Message::where('sender_id', $teacher->id)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->count();
                
            $teacher->unread_count = $unreadCount;
            $teacher->has_unread = $unreadCount > 0;
            return $teacher;
        });

        return $this->successResponse($teachers, 'Daftar Guru BK berhasil dimuat');
    }
    public function sendMessage(Request $request, Consultation $consultation)
    {
        if ($consultation->student_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($consultation->status === 'closed') {
            return $this->errorResponse('Consultation is closed', 400);
        }

        $request->validate([
            'message' => 'required',
        ]);

        $message = ConsultationMessage::create([
            'consultation_id' => $consultation->id,
            'sender_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        return $this->successResponse($message, 'Message sent', 201);
    }
}
