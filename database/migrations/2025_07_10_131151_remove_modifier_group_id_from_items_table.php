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
        Schema::table('items', function (Blueprint $table) {
            // Check if the column exists before dropping to prevent errors on re-run
            if (Schema::hasColumn('items', 'modifier_group_id')) {
                $table->dropConstrainedForeignId('modifier_group_id'); // Drops foreign key and column
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Re-add the column and foreign key for rollback
            $table->foreignId('modifier_group_id')->nullable()->constrained('modifier_groups')->onDelete('set null');
        });
    }
};
