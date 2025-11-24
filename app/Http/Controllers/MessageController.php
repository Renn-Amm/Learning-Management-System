<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        // Get all messages involving this user
        $messages = Message::where('from_id', $userId)
            ->orWhere('to_id', $userId)
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group by conversation partner
        $conversations = collect();
        $seenPartners = [];
        
        foreach ($messages as $message) {
            $partnerId = $message->from_id === $userId ? $message->to_id : $message->from_id;
            
            // Skip if we already have this partner
            if (in_array($partnerId, $seenPartners)) {
                continue;
            }
            
            $seenPartners[] = $partnerId;
            $partner = $message->from_id === $userId ? $message->recipient : $message->sender;
            
            if ($partner) {
                $conversations->push([
                    'partner' => $partner,
                    'last_message' => $message,
                ]);
            }
        }

        return view('messages.index', compact('conversations'));
    }

    public function conversation(User $user)
    {
        $currentUserId = auth()->id();
        $partnerId = $user->id;
        
        // Get all messages between these two users
        $messages = Message::where(function ($q) use ($currentUserId, $partnerId) {
            $q->where('from_id', $currentUserId)->where('to_id', $partnerId);
        })->orWhere(function ($q) use ($currentUserId, $partnerId) {
            $q->where('from_id', $partnerId)->where('to_id', $currentUserId);
        })->orderBy('created_at', 'asc')->get();

        return view('messages.conversation', compact('user', 'messages'));
    }

    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'message_text' => 'required|string',
        ]);

        Message::create([
            'from_id' => auth()->id(),
            'to_id' => $user->id,
            'title' => 'Conversation',
            'subject' => 'Message',
            'message_text' => $validated['message_text'],
        ]);

        return redirect()->route('messages.conversation', $user)->with('success', 'Message sent.');
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->isStudent()) {
            $users = User::where('role', 'teacher')->get();
        } else {
            $users = User::where('role', 'student')->get();
        }

        return view('messages.new', compact('users'));
    }
}
