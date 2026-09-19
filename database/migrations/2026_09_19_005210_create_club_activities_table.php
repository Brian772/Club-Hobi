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
        Schema::create('club_activities', function (Blueprint $table) {
            $table->uuid('id')->primary;    
            $table->foreignUuid('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->string('action');
            $table->text('target_type')->nullable();
            $table->text('target_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_activity');
    }
};
