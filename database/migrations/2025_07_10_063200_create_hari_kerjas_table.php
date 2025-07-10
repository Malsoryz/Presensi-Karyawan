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
        Schema::create('hari_kerja', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('bulan');
            $table->tinyInteger('total_hari');
            $table->tinyInteger('total_hari_minggu');
            $table->tinyInteger('total_hari_libur_nasional');
            $table->tinyInteger('total_hari_libur');
            $table->tinyInteger('total_hari_kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hari_kerja');
    }
};
