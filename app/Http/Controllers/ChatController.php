<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        // Obtener usuarios con los que se ha hablado o todos los docentes/coords
        $users = User::where('id', '!=', Auth::id())->get();
        return view('chat.index', compact('users'));
    }

    public function show(User $user): \Illuminate\View\View
    {
        $myProfileId = Auth::user()->profile->id;
        $otherProfileId = $user->profile->id;

        $messages = Message::where(function($q) use ($myProfileId, $otherProfileId) {
            $q->where('sender_id', $myProfileId)->where('receiver_id', $otherProfileId);
        })->orWhere(function($q) use ($myProfileId, $otherProfileId) {
            $q->where('sender_id', $otherProfileId)->where('receiver_id', $myProfileId);
        })->orderBy('created_at', 'asc')->get();

        // Marcar como leídos
        Message::where('sender_id', $otherProfileId)
               ->where('receiver_id', $myProfileId)
               ->whereNull('read_at')
               ->update(['read_at' => now()]);

        return view('chat.show', compact('user', 'messages'));
    }

    public function store(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['content' => 'required']);

        Message::create([
            'sender_id' => Auth::user()->profile->id,
            'receiver_id' => $user->profile->id,
            'content' => $request->content,
        ]);

        return back();
    }
}
