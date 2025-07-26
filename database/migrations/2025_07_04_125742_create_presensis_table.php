<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\Presensi\JenisPresensi;
use App\Enums\Presensi\StatusPresensi;

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
            $table->enum('jenis_presensi', JenisPresensi::toArray());
            $table->timestampTz('tanggal')->useCurrent();
            $table->enum('status', StatusPresensi::toArray());
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
