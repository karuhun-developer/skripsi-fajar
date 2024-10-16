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
        Schema::create('bandwidths', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('rate_download');
            $table->enum('rate_download_unit', ['Mbps', 'Kbps', 'Gbps'])->default('Mbps');
            $table->integer('rate_upload');
            $table->enum('rate_upload_unit', ['Mbps', 'Kbps', 'Gbps'])->default('Mbps');
            $table->string('burst');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bandwidths');
    }
};
