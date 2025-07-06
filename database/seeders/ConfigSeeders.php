<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('app_configs')->insert([
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
        ]);
    }
}
