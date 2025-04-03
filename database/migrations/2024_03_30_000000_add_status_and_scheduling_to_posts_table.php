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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('content');
            $table->timestamp('published_at')->nullable()->after('status');
            $table->timestamp('scheduled_at')->nullable()->after('published_at');
            $table->string('thumbnail_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['status', 'published_at', 'scheduled_at', 'thumbnail_path']);
        });
    }
}; 