<?php

namespace App\Http\Controllers;

use App\Mail\NewMessageReceived;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    use AuthorizesRequests;
    
    public function index()
    {
        $userId = auth()->id();
        
        // Get all messages involving this user with eager loading (excluding hidden)
        $messages = Message::where(function($q) use ($userId) {
                $q->where('from_id', $userId)
                  ->orWhere('to_id', $userId);
            })
            ->visibleFor($userId) // Only show messages not hidden by current user
            ->with(['sender', 'recipient']) // Eager load to prevent N+1
            ->orderBy('created_at', 'desc')
            ->get();
        
        $conversations = collect();
        $seenPartners = [];
        
        foreach ($messages as $message) {
            $partnerId = $message->from_id === $userId ? $message->to_id : $message->from_id;
            
            if (in_array($partnerId, $seenPartners)) {
                continue;
            }
            
            $seenPartners[] = $partnerId;
            $partner = $message->from_id === $userId ? $message->recipient : $message->sender;
            
            $unreadCount = Message::where('from_id', $partnerId)
                ->where('to_id', $userId)
                ->unread()
                ->count();
            
            if ($partner) {
                $conversations->push([
                    'partner' => $partner,
                    'last_message' => $message,
                    'unread_count' => $unreadCount,
                ]);
            }
        }

        return view('messages.index', compact('conversations'));
    }

    public function conversation(User $user)
    {
        $this->authorize('viewConversation', [Message::class, $user]);

        $currentUserId = auth()->id();
        $partnerId = $user->id;
        
        // Get all messages between these two users (excluding hidden)
        $messages = Message::where(function ($q) use ($currentUserId, $partnerId) {
            $q->where('from_id', $currentUserId)->where('to_id', $partnerId);
        })->orWhere(function ($q) use ($currentUserId, $partnerId) {
            $q->where('from_id', $partnerId)->where('to_id', $currentUserId);
        })
        ->visibleFor($currentUserId) // Only show messages not hidden by current user
        ->orderBy('created_at', 'asc')->get();

        Message::where('from_id', $partnerId)
            ->where('to_id', $currentUserId)
            ->unread()
            ->update(['read_at' => now()]);

        return view('messages.conversation', compact('user', 'messages'));
    }

    public function show(Message $message)
    {
        $this->authorize('view', $message);

        return view('messages.show', compact('message'));
    }

    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'message_text' => 'required|string',
        ]);

        $message = Message::create([
            'from_id' => auth()->id(),
            'to_id' => $user->id,
            'title' => 'Conversation',
            'subject' => 'Message',
            'message_text' => $validated['message_text'],
        ]);

        try {
            $message->load(['sender', 'recipient']);
            Mail::to($user->email)->send(new NewMessageReceived($message));
            \Log::info('Message email sent', [
                'recipient' => $user->email,
                'sender' => auth()->user()->name,
                'message_preview' => substr($validated['message_text'], 0, 50)
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send message email', [
                'error' => $e->getMessage(),
                'recipient' => $user->email
            ]);
        }

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

    public function edit(Message $message)
    {
        $this->authorize('view', $message);

        if ($message->from_id !== auth()->id()) {
            abort(403, 'You can only edit messages you sent.');
        }

        return view('messages.edit', compact('message'));
    }

    public function update(Request $request, Message $message)
    {
        $this->authorize('view', $message);

        if ($message->from_id !== auth()->id()) {
            abort(403, 'You can only update messages you sent.');
        }

        $validated = $request->validate([
            'message_text' => 'required|string',
        ]);

        $message->update($validated);

        return redirect()->route('messages.conversation', $message->recipient)
            ->with('success', 'Message updated successfully.');
    }

    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);

        $recipientId = $message->to_id === auth()->id() ? $message->from_id : $message->to_id;
        $recipient = User::find($recipientId);
        
        $message->delete();

        return redirect()->route('messages.conversation', $recipient)
            ->with('success', 'Message deleted successfully.');
    }

    /**
     * Hide entire conversation with a user (personal delete).
     * Only hides messages for current user, other user still sees them.
     */
    public function deleteConversation(User $user)
    {
        $currentUserId = auth()->id();
        $partnerId = $user->id;

        // Hide all messages between these two users (for current user only)
        $messages = Message::where(function ($q) use ($currentUserId, $partnerId) {
            $q->where('from_id', $currentUserId)->where('to_id', $partnerId);
        })->orWhere(function ($q) use ($currentUserId, $partnerId) {
            $q->where('from_id', $partnerId)->where('to_id', $currentUserId);
        })->get();

        foreach ($messages as $message) {
            $message->hideFor($currentUserId);
        }

        return redirect()->route('messages.index')
            ->with('success', 'Conversation hidden successfully.');
    }
}
