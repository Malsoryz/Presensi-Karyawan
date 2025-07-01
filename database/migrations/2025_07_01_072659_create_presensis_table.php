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
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_karyawan', length: 50);
            $table->enum('jenis_presensi', [
                'desktop',
                'mobile',
            ]);
            $table->timestampTz('tanggal');
            $table->string('status_validasi_jaringan', length: 255);
            $table->string('alamat_perangkat', length: 25);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
