<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * MWA2 REQUIREMENT: Usage Tracking (Challenging Level)
 * 
 * Tracks user actions for analytics and teacher dashboard insights.
 * Actions tracked: course_enrolled, lesson_viewed, message_sent, course_completed
 */
class UserActivity extends Model
{
    const UPDATED_AT = null; // Only track creation time
    
    protected $fillable = [
        'user_id',
        'action',
        'trackable_type',
        'trackable_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    // Action constants for consistency
    const ACTION_COURSE_ENROLLED = 'course_enrolled';
    const ACTION_LESSON_VIEWED = 'lesson_viewed';
    const ACTION_MESSAGE_SENT = 'message_sent';
    const ACTION_COURSE_COMPLETED = 'course_completed';
    const ACTION_FILE_DOWNLOADED = 'file_downloaded';

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tracked model (Course, Lesson, Message, etc.)
     */
    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Helper: Log a user activity
     */
    public static function log(int $userId, string $action, $trackable = null, array $metadata = []): self
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'trackable_type' => $trackable ? get_class($trackable) : null,
            'trackable_id' => $trackable?->id,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Scope: Get activities for a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get activities by action type
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Get recent activities
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
