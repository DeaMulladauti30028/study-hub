<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('group_materials', function (Blueprint $table) {
            $table->timestamp('pinned_at')->nullable()->after('mime_type');
            $table->index('pinned_at');
        });
    }

    public function down(): void {
        Schema::table('group_materials', function (Blueprint $table) {
            $table->dropIndex(['pinned_at']);
            $table->dropColumn('pinned_at');
        });
    }
};
