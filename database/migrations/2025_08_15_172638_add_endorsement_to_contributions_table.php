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
        Schema::table('contributions', function (Blueprint $table) {
            $table->boolean('is_accepted')->default(false);
            $table->foreignId('accepted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('accepted_at')->nullable();
        
            $table->index(['study_group_id', 'is_accepted', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            //
        });
    }
};
