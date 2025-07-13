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
        Schema::create('branch_modifier_group', function (Blueprint $table) {
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('modifier_group_id')->constrained()->onDelete('cascade');
            $table->primary(['branch_id', 'modifier_group_id']); // Composite primary key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_modifier_group', function (Blueprint $table) {
            //
        });
    }
};
