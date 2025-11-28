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
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'hidden_for_users' => 'array',
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
        });
    }
}
