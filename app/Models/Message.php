<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'from_id',
        'to_id',
        'title',
        'subject',
        'message_text',
        'read_at',
        'hidden_for_users',
        'deleted_by_sender_at',
        'deleted_by_receiver_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'hidden_for_users' => 'array',
        'deleted_by_sender_at' => 'datetime',
        'deleted_by_receiver_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'to_id');
    }

    // Scope for unread messages
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // Check if message is read
    public function isRead()
    {
        return !is_null($this->read_at);
    }

    // Mark message as read
    public function markAsRead()
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    // Check if message is hidden for a specific user
    public function isHiddenFor($userId)
    {
        return in_array($userId, $this->hidden_for_users ?? []);
    }

    // Hide message for a specific user
    public function hideFor($userId)
    {
        $hiddenUsers = $this->hidden_for_users ?? [];
        if (!in_array($userId, $hiddenUsers)) {
            $hiddenUsers[] = $userId;
            $this->update(['hidden_for_users' => $hiddenUsers]);
        }
    }

    // Scope to exclude messages hidden for a specific user
    public function scopeVisibleFor($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->whereNull('hidden_for_users')
              ->orWhereRaw('(hidden_for_users IS NOT NULL AND hidden_for_users NOT LIKE ?)', ['%"' . $userId . '"%']);
        })
        // Per-user soft delete: hide if sender deleted and user is sender
        ->where(function($q) use ($userId) {
            $q->where(function($inner) use ($userId) {
                $inner->where('from_id', $userId)->whereNull('deleted_by_sender_at');
            })->orWhere(function($inner) use ($userId) {
                $inner->where('to_id', $userId)->whereNull('deleted_by_receiver_at');
            })->orWhere(function($inner) use ($userId) {
                $inner->where('from_id', '!=', $userId)->where('to_id', '!=', $userId);
            });
        });
    }

    /**
     * Check if message is soft deleted for a specific user.
     * Sender checks deleted_by_sender_at, receiver checks deleted_by_receiver_at.
     */
    public function isDeletedFor($userId)
    {
        if ($this->from_id === $userId) {
            return !is_null($this->deleted_by_sender_at);
        }
        if ($this->to_id === $userId) {
            return !is_null($this->deleted_by_receiver_at);
        }
        return false;
    }

    /**
     * Soft delete message for a specific user.
     * Sets the appropriate timestamp based on whether user is sender or receiver.
     * Permanently deletes if both users have deleted.
     */
    public function softDeleteFor($userId)
    {
        if ($this->from_id === $userId) {
            $this->deleted_by_sender_at = now();
        } elseif ($this->to_id === $userId) {
            $this->deleted_by_receiver_at = now();
        }
        $this->save();

        // Permanent delete when both users have deleted
        if (!is_null($this->deleted_by_sender_at) && !is_null($this->deleted_by_receiver_at)) {
            $this->delete();
            return true;
        }
        return false;
    }
}
