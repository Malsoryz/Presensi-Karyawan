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
        Schema::create('tipe', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tipe')->unique();
            $table->boolean('wajib_upload')->default(false);
            $table->timestamps();
        });

        DB::table('tipe')->insert([
            [
                'nama_tipe' => 'Karyawan tetap',
                'wajib_upload' => false,
            ],
            [
                'nama_tipe' => 'Magang',
                'wajib_upload' => true,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipe');
    }
};
