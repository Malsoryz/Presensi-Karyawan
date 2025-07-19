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
        $users = DB::table('users')->select('id', 'name')->where('jabatan', '!=', 'admin')->get();

        $status = ['masuk', 'terlambat', 'ijin', 'sakit', 'tidak_masuk'];
        $sesiPresensi = ['pagi', 'siang'];

        foreach ($users as $user) {
            foreach ($sesiPresensi as $sesi) {
                for ($i = 1; $i <= 40; $i++) { 
                    DB::table('presensi')->insert([
                        'nama_karyawan' => $user->name,
                        'jenis_presensi' => $sesi,
                        'tanggal' => Carbon::now('Asia/Makassar')->subDays($i),
                        'status' => $status[array_rand($status)],
                        'ip_address' => '192.168.1.'.$i,
                        'user_id' => $user->id,
                    ]);
                }
            }
        }
    }
}
