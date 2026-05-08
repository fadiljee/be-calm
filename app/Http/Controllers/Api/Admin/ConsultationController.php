<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationMessage;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $consultations = Consultation::with('student')->latest()->get();
        return $this->successResponse($consultations, 'Consultations retrieved');
    }

    public function reply(Request $request, Consultation $consultation)
    {
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

        return $this->successResponse($message, 'Reply sent', 201);
    }

    public function close(Consultation $consultation)
    {
        $consultation->update(['status' => 'closed']);
        return $this->successResponse($consultation, 'Consultation closed');
    }
}
