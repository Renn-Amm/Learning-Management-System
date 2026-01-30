<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'progress',
        'is_completed',
        'viewed_lessons',
        'last_accessed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'viewed_lessons' => 'array',
        'last_accessed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
