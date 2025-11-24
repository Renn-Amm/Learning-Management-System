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
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'to_id');
    }
}
