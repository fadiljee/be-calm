<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    use ApiResponse;

    public function getMessages(Request $request, $receiverId)
    {
        $userId = auth()->id();

        // Tandai semua pesan dari lawan bicara sebagai sudah dibaca
        Message::where('sender_id', $receiverId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Ambil semua pesan dalam percakapan ini
        $messages = Message::where(function ($query) use ($userId, $receiverId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $receiverId);
            })
            ->orWhere(function ($query) use ($userId, $receiverId) {
                $query->where('sender_id', $receiverId)
                      ->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return $this->successResponse($messages, 'Messages retrieved');
    }

    public function getUnreadCount(Request $request)
    {
        $userId = $request->user()->id;

        $unreadCount = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        return $this->successResponse([
            'has_unread'  => $unreadCount > 0,
            'unread_count' => $unreadCount,
        ], 'Unread count retrieved');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'required|string',
        ]);

        $message = Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
        ]);

        return $this->successResponse($message, 'Pesan terkirim', 201);
    }
}