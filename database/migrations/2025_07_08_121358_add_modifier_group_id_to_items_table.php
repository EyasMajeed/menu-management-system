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
            $table->foreignId('modifier_group_id')
                  ->nullable()
                  ->constrained('modifier_groups') // Constrain to modifier_groups table
                  ->onDelete('set null') // Set to null if the associated modifier group is deleted
                  ->after('menu_id'); // Place it after menu_id, or adjust as needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            //
        });
    }
};
