<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('deleted_by_sender_at')->nullable()->after('hidden_for_users');
            $table->timestamp('deleted_by_receiver_at')->nullable()->after('deleted_by_sender_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['deleted_by_sender_at', 'deleted_by_receiver_at']);
        });
    }
};
