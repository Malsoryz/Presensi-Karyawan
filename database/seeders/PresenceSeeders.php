<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PresenceSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 5; $i++) { 
            DB::table('presensi')->insert([
                'nama_karyawan' => Str::random(10),
                'jenis_presensi' => 'pagi',
                'tanggal' => Carbon::now('Asia/Makassar'),
                'ip_address' => Str::random(10)
            ]);
        }
    }
}
