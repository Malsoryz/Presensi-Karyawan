<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuestUsers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Ojt',
            'email' => 'test@example.com',
            'password' => Hash::make('test12345'),
            'address' => 'somewhere',
            'birth_date' => '2000-10-10',
            'gender' => 'male',
            'phone_number' => '628794703970'
        ]);
    }
}
