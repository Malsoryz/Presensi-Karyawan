<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Config;
use App\Models\Presensi;

use App\Enums\StatusPresensi;

// gunakan enum untuk mempermudah
// dalam mengubah value
enum SesiPresensi: string
{
    case PAGI = 'pagi';
    case SIANG = 'siang';
}

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

        // arrow function
        $cftsFromConfig = fn (string $name, string $default) => Carbon::createFromTimeString(Config::getTime($name, $default), $timezone);

        // mendapatkan value dan setting variable untuk jam presensi
        $now = now($timezone);

        $pagiMulai = $cftsFromConfig('presensi_pagi_mulai', '08:00:00');
        $pagiSelesai = $cftsFromConfig('presensi_pagi_selesai', '09:00:00');
        $siangMulai = $cftsFromConfig('presensi_siang_mulai', '14:00:00');
        $siangSelesai = $cftsFromConfig('presensi_siang_selesai', '15:00:00');
        $toleransi = (int) Config::get('toleransi_presensi', 0);
    
        // dengan waktu toleransi
        $pagiSelesaiToleransi = $pagiSelesai->copy()->addMinutes($toleransi);
        $siangSelesaiToleransi = $siangSelesai->copy()->addMinutes($toleransi);
    
        $isPagiSession = $now->between($pagiMulai, $pagiSelesaiToleransi);
        $isSiangSession = $now->between($siangMulai, $siangSelesaiToleransi);
    
        // tanpa waktu toleransi
        $isPagiAsli = $now->between($pagiMulai, $pagiSelesai);
        $isSiangAsli = $now->between($siangMulai, $siangSelesai);

        // cek untuk mendapatkan status presensi antara masuk dan terlambat
        $presenceStatus = StatusPresensi::TIDAK_MASUK->value;
        if ($isPagiAsli || $isSiangAsli) {
            $presenceStatus = StatusPresensi::MASUK->value;
        } 
        if ((!$isPagiAsli && $isPagiSession) || (!$isSiangAsli && $isSiangSession)) {
            $presenceStatus = StatusPresensi::TERLAMBAT->value;
        }

        // mendapatkan sesi saat ini, null jika di luar jam presensi
        $session = null;
        if ($isPagiSession) $session = SesiPresensi::PAGI->value;
        if ($isSiangSession) $session = SesiPresensi::SIANG->value;

        // mendapatkan sesi jika di luar dari presensi
        $isPagi = $now->between($pagiMulai, $siangMulai) ? SesiPresensi::PAGI->value : SesiPresensi::SIANG->value;
        
        // mendapatkan nama user yang tidak presensi
        $users = User::pluck('name')->toArray();
        $presenceUsers = Presensi::whereDate('tanggal', $now->toDateString())
            ->where('jenis_presensi', '=', $isPagi)
            ->pluck('nama_karyawan')
            ->toArray();

        $notPresenceUsers = array_diff($users, $presenceUsers);

        // test
        // $this->info(var_dump($notPresenceUsers));
        // $this->info(var_dump($presenceStatus));

        // jika semua nya telah presensi
        if (count($notPresenceUsers) === 0) {
            return $this->info('Semua Users telah melakukan presensi');
        }

        // memberitahu siapa saja yang belum presensi
        $this->info('Belum melakukan presensi:');
        foreach ($notPresenceUsers as $user) {
            $this->info($user);
        }

        // beritahu status yang akan di dapatkan saat ini
        if ($presenceStatus === StatusPresensi::MASUK->value) {
            return $this->info('bisa melakukan presensi');
        } elseif ($presenceStatus === StatusPresensi::TERLAMBAT->value) {
            return $this->info('bisa melakukan presensi, tapi dianggap terlambat');
        }

        // jika sama sekali tidak presensi
        foreach ($notPresenceUsers as $user) {
            $this->info("$user tidak masuk hari ini.");

            Presensi::create([
                'nama_karyawan' => $user,
                'jenis_presensi' => $isPagi,
                'status' => StatusPresensi::TIDAK_MASUK->value,
                'ip_address' => '0',
            ]);
        }
    }
}
