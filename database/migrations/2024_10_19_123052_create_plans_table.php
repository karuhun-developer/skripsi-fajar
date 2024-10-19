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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bandwidth_id')->constrained();
            $table->string('name');
            $table->foreignId('pool_id')->constrained();
            $table->bigInteger('price');
            $table->enum('type', ['PPPOE', 'HOTSPOT']);
            $table->integer('validity');
            $table->enum('validity_unit', ['min', 'hour', 'day', 'month', 'year']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
