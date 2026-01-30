<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * MWA2 REQUIREMENT: Usage Tracking (Challenging Level)
     * Tracks important user actions for analytics and teacher dashboard insights
     */
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // course_enrolled, lesson_viewed, message_sent, course_completed, etc.
            $table->string('trackable_type')->nullable(); // Course, Lesson, Message, etc.
            $table->unsignedBigInteger('trackable_id')->nullable(); // ID of the related model
            $table->json('metadata')->nullable(); // Additional context (course title, lesson name, etc.)
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes for performance
            $table->index(['user_id', 'action']);
            $table->index(['trackable_type', 'trackable_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
