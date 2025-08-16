<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('study_group_user', function (Blueprint $table) {
            $table->boolean('is_moderator')->default(false)->after('user_id');
            $table->index('is_moderator');
        });
    }

    public function down(): void
    {
        Schema::table('study_group_user', function (Blueprint $table) {
            $table->dropIndex(['is_moderator']);
            $table->dropColumn('is_moderator');
        });
    }
};
