<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('presensis')->insert([
            'nama_karyawan' => Str::random(10),
            'jenis_presensi' => 'mobile',
            'tanggal' => Carbon::now(),
            'status_validasi_jaringan' => "terverifikasi",
            'alamat_perangkat' => Str::random(10), 
        ]);
    }
}
