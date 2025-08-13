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
    Schema::create('group_sessions', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->foreignId('study_group_id')->constrained()->cascadeOnDelete();
        $table->dateTime('starts_at');                 
        $table->unsignedInteger('duration_minutes');   
        $table->string('video_url')->nullable();      
        $table->text('notes')->nullable();             
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_sessions');
    }
};
