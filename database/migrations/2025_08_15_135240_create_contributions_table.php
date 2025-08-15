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
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
        
            // Ownership & context
            $table->foreignId('study_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        
            // Core fields
            $table->string('title', 120);
            $table->text('content')->nullable();        // text body (optional)
            $table->string('file_path')->nullable();    // stored on private disk (optional)
            $table->string('mime_type', 191)->nullable();
        
            // Light auditing
            $table->boolean('is_edited')->default(false);
        
            $table->timestamps();
        
            // Useful index for lists
            $table->index(['study_group_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
