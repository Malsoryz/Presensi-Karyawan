<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiDummy extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->select('name')->get();

        $status = ['masuk', 'terlambat', 'ijin', 'sakit', 'tidak_masuk'];

        foreach ($users as $user) {
            for ($i = 0; $i < 40; $i++) { 
                DB::table('presensi')->insert([
                    'nama_karyawan' => $user->name,
                    'jenis_presensi' => 'pagi',
                    'tanggal' => Carbon::now('Asia/Makassar')->addDays($i),
                    'status' => $status[array_rand($status)],
                    'ip_address' => '192.168.1.'.$i,
                ]);
            }
        }
    }
}
