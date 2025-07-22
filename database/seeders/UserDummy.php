<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserDummy extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Ali',
                'email' => 'ali@example.com',
            ],
            [
                'name' => 'Budi',
                'email' => 'budi@example.com',
            ],
            [
                'name' => 'Caca',
                'email' => 'caca@example.com',
            ],
            [
                'name' => 'Denis',
                'email' => 'denis@example.com',
            ],
            [
                'name' => 'Erfan',
                'email' => 'erfan@example.com',
            ],
        ];

        foreach ($data as $user) {
            DB::table('users')->insert([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('defaultpass'),
            ]);
        }
    }
}
