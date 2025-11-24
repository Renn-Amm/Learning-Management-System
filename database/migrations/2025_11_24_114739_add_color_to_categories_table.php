<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('color_code', 7)->default('#000000')->after('name');
        });

        // Set fixed colors for existing categories
        DB::table('categories')->where('name', 'Programming')->update(['color_code' => '#3A86FF']);
        DB::table('categories')->where('name', 'Math')->update(['color_code' => '#06FFA5']);
        DB::table('categories')->where('name', 'Business')->update(['color_code' => '#FF006E']);
        DB::table('categories')->where('name', 'Design')->update(['color_code' => '#FFBE0B']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('color_code');
        });
    }
};
