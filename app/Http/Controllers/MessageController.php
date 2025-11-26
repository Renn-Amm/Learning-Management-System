<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of conversations.
     * Eager load sender and recipient to prevent N+1 queries.
     */
    public function index()
    {
        $userId = auth()->id();
        
        // Get all messages involving this user with eager loading
        $messages = Message::where('from_id', $userId)
            ->orWhere('to_id', $userId)
            ->with(['sender', 'recipient']) // Eager load to prevent N+1
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
            
            // Count unread messages from this partner
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

    /**
     * Display conversation with a specific user.
     * Authorization: User can only view their own conversations.
     */
    public function conversation(User $user)
    {
        // Authorization: Check if user can view conversation
        $this->authorize('viewConversation', [Message::class, $user]);

        $currentUserId = auth()->id();
        $partnerId = $user->id;
        
        // Get all messages between these two users
        $messages = Message::where(function ($q) use ($currentUserId, $partnerId) {
            $q->where('from_id', $currentUserId)->where('to_id', $partnerId);
        })->orWhere(function ($q) use ($currentUserId, $partnerId) {
            $q->where('from_id', $partnerId)->where('to_id', $currentUserId);
        })->orderBy('created_at', 'asc')->get();

        // Mark all messages from partner as read
        Message::where('from_id', $partnerId)
            ->where('to_id', $currentUserId)
            ->unread()
            ->update(['read_at' => now()]);

        return view('messages.conversation', compact('user', 'messages'));
    }

    /**
     * Show a single message (for CRUD completeness).
     */
    public function show(Message $message)
    {
        // Authorization: Only sender or recipient can view
        $this->authorize('view', $message);

        return view('messages.show', compact('message'));
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

    /**
     * Show the form for editing a message (CRUD completeness).
     * Only the sender can edit their own messages.
     */
    public function edit(Message $message)
    {
        // Authorization: Only sender can edit
        $this->authorize('view', $message);

        if ($message->from_id !== auth()->id()) {
            abort(403, 'You can only edit messages you sent.');
        }

        return view('messages.edit', compact('message'));
    }

    /**
     * Update a message (CRUD completeness).
     * Only the sender can update their own messages.
     */
    public function update(Request $request, Message $message)
    {
        // Authorization: Only sender can update
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

    /**
     * Delete a message (CRUD completeness).
     * Only the sender can delete their own messages.
     */
    public function destroy(Message $message)
    {
        // Authorization: Only sender can delete
        $this->authorize('delete', $message);

        $recipientId = $message->to_id;
        $message->delete();

        return redirect()->route('messages.conversation', $recipientId)
            ->with('success', 'Message deleted successfully.');
    }
}
