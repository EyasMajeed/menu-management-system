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
        Schema::create('item_modifier_group', function (Blueprint $table) {
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('modifier_group_id')->constrained()->onDelete('cascade');
            $table->primary(['item_id', 'modifier_group_id']); // Composite primary key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_modifier_group');
    }
};
