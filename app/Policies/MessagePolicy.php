<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    /**
     * Determine if the user can view the message.
     * Only sender or recipient can view the message.
     */
    public function view(User $user, Message $message): bool
    {
        return $user->id === $message->from_id || $user->id === $message->to_id;
    }

    /**
     * Determine if the user can create messages.
     * All authenticated users can send messages.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view conversation with another user.
     * All authenticated users can view their own conversations.
     */
    public function viewConversation(User $user, User $otherUser): bool
    {
        return $user->id !== $otherUser->id; // Can't message yourself
    }

    /**
     * Determine if the user can delete the message.
     * Both sender and receiver can soft-delete the message for themselves.
     */
    public function delete(User $user, Message $message): bool
    {
        return $user->id === $message->from_id || $user->id === $message->to_id;
    }
}
