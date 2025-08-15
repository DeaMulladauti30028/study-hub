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
        Schema::create('contribution_helpfuls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        
            $table->unique(['contribution_id', 'user_id']); // one helpful per user per contribution
            $table->index('contribution_id');               // quick counts
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contribution_helpfuls');
    }
};
