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
            $table->json('name_json')->nullable(); // Step 1: Add JSON column
        });

        // Step 2: Migrate old `name` string data into JSON format (e.g. {"en": "Burger", "ar": "برجر"})
        DB::table('items')->get()->each(function ($item) {
            DB::table('items')
              ->where('id', $item->id)
              ->update(['name_json' => json_encode(['en' => $item->name, 'ar' => $item->name])]);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('name');              // Step 3: Drop old column
            $table->renameColumn('name_json', 'name'); // Step 4: Rename new column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('name')->nullable();
        });
    }
};
