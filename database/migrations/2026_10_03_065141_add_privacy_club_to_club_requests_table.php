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
        Schema::table('club_requests', function (Blueprint $table) {
            $table->enum('privacy_club', ['public', 'private'])->default('public')->after('cover_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('club_requests', function (Blueprint $table) {
            //
        });
    }
};
