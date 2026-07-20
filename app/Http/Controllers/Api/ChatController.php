<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\ScreeningHistory;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    use ApiResponse;

    // Mengambil percakapan antara siswa dan guru
    public function getMessages($receiverId)
    {
        $senderId = auth()->id();

        // Tandai pesan sebagai terbaca jika kita adalah penerimanya
        Message::where('sender_id', $receiverId)
            ->where('receiver_id', $senderId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Mengambil pesan yang dikirim ATAU diterima oleh user yang login
        $messages = Message::where(function ($q) use ($senderId, $receiverId) {
            $q->where('sender_id', $senderId)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($senderId, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $senderId);
        })->orderBy('created_at', 'asc')->get();

        return $this->successResponse($messages, 'Pesan berhasil dimuat');
    }

    public function unreadCount()
    {
        $userId = auth()->id();
        $count = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();
            
        return $this->successResponse([
            'unread_count' => $count,
            'has_unread' => $count > 0,
        ], 'Unread count retrieved');
    }

    // Mengirim pesan
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return $this->successResponse($message, 'Pesan terkirim', 201);
    }

    // Mengambil info peserta chat (profil + deteksi terakhir)
    // Digunakan oleh Guru BK untuk melihat status siswa di header room chat
    public function participantInfo($receiverId)
    {
        $participant = User::findOrFail($receiverId);

        // Ambil riwayat screening terakhir dari peserta
        $latestScreening = ScreeningHistory::where('student_id', $receiverId)
            ->latest()
            ->first();

        $info = [
            'id'          => $participant->id,
            'name'        => $participant->name,
            'role'        => $participant->role,
            'nisn'        => $participant->nisn ?? null,
            'kelas'       => $participant->kelas ?? null,
            'last_screening' => $latestScreening ? [
                'conclusion'  => $latestScreening->conclusion,
                'percentage'  => $latestScreening->grand_mean, // grand_mean menyimpan persentase
                'total_score' => $latestScreening->total_score,
                'created_at'  => $latestScreening->created_at,
            ] : null,
        ];

        return $this->successResponse($info, 'Informasi peserta chat berhasil dimuat');
    }
}