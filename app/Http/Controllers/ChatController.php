<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Chat feature not implemented.'], 501);
    }

    public function searchUsers(Request $request)
    {
        return response()->json(['message' => 'Chat feature not implemented.'], 501);
    }

    public function getMessages(Request $request)
    {
        return response()->json(['message' => 'Chat feature not implemented.'], 501);
    }

    public function sendMessage(Request $request)
    {
        return response()->json(['message' => 'Chat feature not implemented.'], 501);
    }

    public function getUnreadCount(Request $request)
    {
        return response()->json(['count' => 0], 200);
    }

    public function markAsRead(Request $request)
    {
        return response()->json(['message' => 'Chat feature not implemented.'], 501);
    }

    public function deleteMessage(Request $request)
    {
        return response()->json(['message' => 'Chat feature not implemented.'], 501);
    }
}
