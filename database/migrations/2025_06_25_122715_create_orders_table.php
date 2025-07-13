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
        Schema::dropIfExists('orders');


        Schema::create('orders', function (Blueprint $table) {
            
            $table->id();
            $table->timestamps();//created at
            $table->string('app');
            $table->string('resturant');
            $table->string('status');
            $table->string('details');
        });
    }

    public function down(): void
{
    Schema::dropIfExists('orders');
}
};
