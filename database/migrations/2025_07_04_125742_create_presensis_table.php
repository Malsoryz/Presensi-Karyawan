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
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_karyawan', length: 50);
            $table->enum('jenis_presensi', ['pagi', 'siang']);
            $table->timestampTz('tanggal')->useCurrent();
            $table->enum('status', [
                'masuk',
                'terlambat',
                'ijin',
                'sakit',
                'tidak_masuk'
            ]);
            $table->string('ip_address', length: 25);
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
