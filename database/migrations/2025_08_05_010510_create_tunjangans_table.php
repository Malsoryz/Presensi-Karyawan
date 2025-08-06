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
        Schema::create('tunjangan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', length: 255);
            $table->string('jumlah');
            $table->unsignedBigInteger('jabatan_id');
            $table->foreign('jabatan_id')
                ->references('id')
                ->on('jabatan')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunjangan');
    }
};
