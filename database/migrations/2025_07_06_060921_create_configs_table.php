<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('value')->nullable();
        });

        DB::table('app_configs')->insert([
            [
                'name' => 'timezone',
                'value' => 'Asia/Makassar'
            ],
            [
                'name' => 'presensi_pagi_mulai',
                'value' => '08:00:00',
            ],
            [
                'name' => 'presensi_pagi_selesai',
                'value' => '09:00:00',
            ],
            [
                'name' => 'presensi_siang_mulai',
                'value' => '14:00:00',
            ],
            [
                'name' => 'presensi_siang_selesai',
                'value' => '15:00:00',
            ],
            [
                'name' => 'jam_mulai_kerja',
                'value' => '08:00:00',
            ],
            [
                'name' => 'jam_selesai_istirahat',
                'value' => '14:00:00',
            ],
            [
                'name' => 'toleransi_presensi',
                'value' => '30',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configs');
    }
};
