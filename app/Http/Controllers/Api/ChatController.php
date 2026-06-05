<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    use ApiResponse;

    // Mengambil percakapan antara siswa dan guru
    public function getMessages($receiverId)
{
    $userId = auth()->id();

    // 1. UPDATE STATUS TERBACA
    // Saat user membuka room chat ini, semua pesan dari lawan bicara (receiverId) 
    // yang ditujukan ke kita (userId) ditandai sudah dibaca.
    \App\Models\Message::where('sender_id', $receiverId)
        ->where('receiver_id', $userId)
        ->where('is_read', false)
        ->update(['is_read' => true]);

    // 2. AMBIL PESAN SEPERTI BIASA
    $messages = \App\Models\Message::where(function ($query) use ($userId, $receiverId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $receiverId);
        })
        ->orWhere(function ($query) use ($userId, $receiverId) {
            $query->where('sender_id', $receiverId)
                  ->where('receiver_id', $userId);
        })
        ->orderBy('created_at', 'asc')
        ->get();

    return response()->json(['data' => $messages]);
}

public function getUnreadCount(Request $request)
{
    $userId = $request->user()->id;

    // Hitung pesan yang masuk ke siswa ini yang status 'is_read'-nya masih false
    $unreadCount = \App\Models\Message::where('receiver_id', $userId)
        ->where('is_read', false)
        ->count();

    return response()->json([
        'has_unread' => $unreadCount > 0,
        'unread_count' => $unreadCount
    ]);
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
}