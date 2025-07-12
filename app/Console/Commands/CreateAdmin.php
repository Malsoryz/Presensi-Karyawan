<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
     /**
     * Signature command.
     * Menerima argumen 'name' dan 'email'.
     */
    protected $signature = 'make:admin {name?} {email?}';

    /**
     * Deskripsi command.
     */
    protected $description = 'Membuat user baru dengan hak akses admin';

    /**
     * Logika utama command.
     */
    public function handle()
    {
        // 1. Ambil argumen dari pengguna
        $name = $this->argument('name') ?? $this->ask('Admin name', 'Admin');
        $email = $this->argument('email') ?? $this->ask('Admin email');

        // 2. Minta password secara rahasia (input tidak akan terlihat)
        $password = $this->secret('Masukkan password untuk user baru');

        // 3. Kumpulkan semua data untuk divalidasi
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ];

        // 4. Buat aturan validasi
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
        ]);

        // 5. Cek jika validasi gagal
        if ($validator->fails()) {
            $this->error('Gagal membuat admin. Periksa error berikut:');
            foreach ($validator->errors()->all() as $error) {
                $this->line(" - " . $error);
            }
            return self::FAILURE;
        }

        // 6. Jika validasi berhasil, buat user
        try {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']), // Selalu hash password!
                'jabatan' => 'admin',
            ]);

            $this->info("User admin '" . $data['name'] . "' berhasil dibuat dengan email '" . $data['email'] . "'.");

        } catch (\Exception $e) {
            $this->error("Terjadi kesalahan saat menyimpan ke database: " . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
