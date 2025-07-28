<?php

use App\Enums\User\Jabatan;
use App\Enums\User\Tipe;
use App\Enums\User\Gender;
use App\Enums\User\Role;

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', Gender::toArray())->nullable(); //changes
            $table->string('phone_number')->nullable();
            $table->string('departmen')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_masuk_sebagai_karyawan')->nullable();
            $table->string('rekening_bank')->nullable();
            $table->boolean('status_approved')->default(0);
            $table->enum('role', Role::toArray());
            $table->unsignedBigInteger('jabatan_id')->nullable();
            $table->foreign('jabatan_id')
                ->references('id')
                ->on('jabatan')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedBigInteger('tipe_id')->nullable();
            $table->foreign('tipe_id')
                ->references('id')
                ->on('tipe')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
