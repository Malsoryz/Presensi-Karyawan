<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Config;
use App\Models\Presensi;

class CheckPresence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presence:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'melakukan cek sesi presensi';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $timezone = Config::get('timezone', 'Asia/Makassar');
        $now = now($timezone);

        $pagiMulai = Carbon::createFromTimeString(Config::getTime('presensi_pagi_mulai', '08:00:00'), $timezone);
        $pagiSelesai = Carbon::createFromTimeString(Config::getTime('presensi_pagi_selesai', '09:00:00'), $timezone);
        $siangMulai = Carbon::createFromTimeString(Config::getTime('presensi_siang_mulai', '14:00:00'), $timezone);
        $siangSelesai = Carbon::createFromTimeString(Config::getTime('presensi_siang_selesai', '15:00:00'), $timezone);
        $toleransi = (int) Config::get('toleransi_presensi', 0);
    
        $pagiSelesaiToleransi = $pagiSelesai->copy()->addMinutes($toleransi);
        $siangSelesaiToleransi = $siangSelesai->copy()->addMinutes($toleransi);
    
        $isPagiSession = $now->between($pagiMulai, $pagiSelesaiToleransi);
        $isSiangSession = $now->between($siangMulai, $siangSelesaiToleransi);
    
        $isPagiAsli = $now->between($pagiMulai, $pagiSelesai);
        $isSiangAsli = $now->between($siangMulai, $siangSelesai);

        $presenceStatus = null;
        if ($isPagiAsli || $isSiangAsli) {
            $presenceStatus = 'masuk';
        } elseif ((!$isPagiAsli && $isPagiSession) || (!$isSiangAsli && $isSiangSession)) {
            $presenceStatus = 'terlambat';
        }

        $session = null;
        if ($isPagiSession) $session = 'pagi';
        if ($isSiangSession) $session = 'siang';

        $isPagi = $now->between($pagiMulai, $siangMulai) ? 'pagi' : 'siang';
        
        //get list of name
        $users = User::pluck('name')->toArray();

        $presenceUsers = Presensi::whereDate('tanggal', $now->toDateString())
            ->where('jenis_presensi', '=', $isPagi)
            ->pluck('nama_karyawan')
            ->toArray();

        $notPresenceUsers = array_diff($users, $presenceUsers);

        // $this->info(var_dump($notPresenceUsers));
        // $this->info(var_dump($isPagi));

        if (count($notPresenceUsers) === 0) {
            return $this->info('Tidak ada users yang tidak presensi hari ini');
        }

        if ($presenceStatus === 'masuk') {
            return $this->info('bisa melakukan presensi');
        } elseif ($presenceStatus === 'terlambat') {
            return $this->info('bisa melakukan presensi tapi dianggap terlambat');
        }

        foreach ($notPresenceUsers as $user) {
            $this->info("$user tidak masuk hari ini.");

            Presensi::create([
                'nama_karyawan' => $user,
                'jenis_presensi' => $isPagi,
                'status' => 'tidak_masuk',
                'ip_address' => '0',
            ]);
        }

        // $isSessionValid = $session !== null;

        // $userName = Auth::user()->name;
        // $today = $now->toDateString();
        // $isPresence = false;

        // if ($now->between($pagiMulai, $siangMulai, false)) {
        //     $isPresence = Presensi::where('nama_karyawan', $userName)
        //         ->where('jenis_presensi', self::SESSION_PAGI)
        //         ->whereDate('tanggal', $today)
        //         ->exists();
        // } else {
        //     $isPresence = Presensi::where('nama_karyawan', $userName)
        //         ->where('jenis_presensi', self::SESSION_SIANG)
        //         ->whereDate('tanggal', $today)
        //         ->exists();
        // }

    }
}
