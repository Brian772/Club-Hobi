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
        Schema::create('media_gallery', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignUuid('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('file_url');
            $table->string('type');
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_gallery');
    }
};
