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
    ];

    protected $casts = [
        'read_at' => 'datetime',
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
}
