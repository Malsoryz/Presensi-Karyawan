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
        Schema::table('presensi', function (Blueprint $table) {
            $table->after('tanggal', function (Blueprint $table) {
                $table->enum('status', [
                    'masuk',
                    'terlambat',
                    'ijin',
                    'sakit',
                    'tidak_masuk'
                ]);
            }); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
