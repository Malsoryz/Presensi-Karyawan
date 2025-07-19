<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\Presensi;
use App\Models\Config;
use App\Models\HariLibur as HL;
use Carbon\Carbon;

use App\Enums\StatusPresensi as SP;
use App\Enums\JenisPresensi as JP;
use App\Enums\SesiPresensi as SPI;

class PresensiControllers extends Controller
{
    public function index()
    {
        $presensi = $this->check();
        $user = $presensi['user']; // Mendapatkan USER
        $now = now($presensi['timezone']);
        $today = $now->toDateString();

        if ($user->isAdmin()) {
            return view('presensi.info', [
                'message' => 'Anda adalah admin, anda tidak perlu melakukan presensi.',
            ]);
        }

        $hariLibur = HL::whereDate('tanggal', $today)->get();
        $isHariLibur = HL::whereDate('tanggal', $today)->exists();
        
        // cek untuk mengetahui apakah libur atau hari minggu
        // if ($now->isSunday() || $isHariLibur) {
        if ($now->isSunday() || $isHariLibur) {
            return view('presensi.info', [
                'presenceSession' => SPI::LIBUR,
                'hariLibur' => $hariLibur,
                'hari' => $now->format('l'),
                'status' => SP::BELUM,
            ]);
        }

        // jika belum mulai
        if ($presensi['presence_session'] === SPI::BELUM_MULAI) {
            return view('presensi.info', [
                'presenceSession' => SPI::BELUM_MULAI,
                'status' => SP::BELUM,
                'presenceStartTime' => $presensi['presence_start_time'],
                'now' => $now,
            ]);
        }
        
        // jika di saat sesi presensi berlangsung tapi belum presensi
        if (!$presensi['is_presence'] && $presensi['presence_session'] === SPI::SESI_PRESENSI) {
            $token = Str::uuid();
            Cache::put("token_{$user->name}_{$token}", true, now()->addMinutes(1));
            $topThree = Presensi::getTotal()->limit(5)->get();
            return view('presensi.index', [
                'presenceSession' => SPI::SESI_PRESENSI,
                'name' => $user->name,
                'token' => $token,
                'topThreePresence' => $topThree, // ambil 3 data presensi teratas
                'status' => SP::BELUM,
            ]);
        }

        // jika telah presensi atau sesi presensi sudah berakhir
        // yang artinya di sini adalah default nya
        // SesiPresensi::SELESAI atau selesai
        $presenceDateTime = Presensi::where('nama_karyawan', $user->name)
            ->where('jenis_presensi', $presensi['presence_type']->value)
            ->whereDate('tanggal', $now->toDateString())
            ->first()
            ->value('tanggal');
        $presenceTime = Carbon::parse($presenceDateTime)->format('H:i:s');

        $presensiUser = Presensi::where([
            'nama_karyawan' => $user->name,
            'jenis_presensi' => $presensi['presence_type'],
        ])->whereDate('tanggal', $today)->first();

        return view('presensi.info', [
            'presenceSession' => SPI::SELESAI,
            'status' => SP::tryFrom($presensiUser->status),
            'presenceTime' => $presenceTime,
        ]);
    }

    public function store(Request $request, string $name, string $token)
    {
        if (!Cache::has("token_{$name}_{$token}")) {
            return redirect()->route('presensi.index')->with('error', 'Token tidak valid atau sudah kadaluarsa.');
        }
        Cache::forget("token_{$name}_{$token}");

        if ($name != Auth::user()->name) {
            return redirect()->route('presensi.index')->with('error', 'Tidak bisa melakukan presensi untuk orang lain.');
        }

        $presensi = $this->check();

        if ($presensi['presence_session'] !== SPI::SESI_PRESENSI ) {
            return redirect()->route('presensi.index')->with('error', 'Sesi presensi tidak valid!');
        }

        if ($presensi['presence_type'] !== JP::NONE || !Auth::user()->isAdmin()) {
            Auth::user()->presensis()->create([
                'nama_karyawan' => $name,
                'jenis_presensi' => $presensi['presence_type']->value,
                'status' => $presensi['presence_status'],
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('presensi.index');
    }

    public function scanCheck()
    {
        $presensi = $this->check();

        return response()->json([
            'is_presence' => $presensi['is_presence'],
        ]);
    }
    
    public function test()
    {
        return var_dump($this->check());
    }

    private function check(): array
    {
        // user
        $user = Auth::user();

        // dapatkan waktu sekarang dan timezone
        $timezone = Config::get('timezone', 'Asia/Makassar');
        $now = now($timezone);

        // arrow function
        $cftsFromConfig = fn (string $name, string $default) => Carbon::createFromTimeString(Config::getTime($name, $default), $timezone);

        // jam presensi
        $pagiMulai = $cftsFromConfig('presensi_pagi_mulai', '08:00:00');
        $pagiSelesai = $cftsFromConfig('presensi_pagi_selesai', '09:00:00');
        $siangMulai = $cftsFromConfig('presensi_siang_mulai', '14:00:00');
        $siangSelesai = $cftsFromConfig('presensi_siang_selesai', '15:00:00');
        $toleransi = (int) Config::get('toleransi_presensi', 0);
        $pulangKerja = Carbon::createFromTimeString('17:00:00', $timezone);

        // waktu presensi
        $presensiSession = SPI::BELUM_MULAI; // satu hari kerja. untuk mengetahui apakah masih bisa presensi # 1
        if ($now->between($pagiMulai, $pulangKerja)) $presensiSession = SPI::SESI_PRESENSI;
        if ($now->gt($pulangKerja)) $presensiSession = SPI::SELESAI;

        $isAfterPagi = $now->between($pagiSelesai->copy()->addMinutes($toleransi), $siangMulai);
        $isAfterSiang = $now->between($siangSelesai->copy()->addMinutes($toleransi), $pulangKerja);

        $statusPresensi = SP::BELUM; // Status Presensi # 2 perlu diambil ->value
        if ($presensiSession) {
            $statusPresensi = SP::MASUK;
        }
        if ($isAfterPagi || $isAfterSiang) {
            $statusPresensi = SP::TERLAMBAT;
        }
        if ($now->gt($pulangKerja)) {
            $statusPresensi = SP::TIDAK_MASUK;
        }
        
        $sessionType = JP::NONE; // mendapatkan sesi sekarang, default value null # 4
        if ($now->between($pagiMulai, $siangMulai)) $sessionType = JP::PAGI;
        if ($now->between($siangMulai, $pulangKerja)) $sessionType = JP::SIANG;

        // cek apakah sudah presensi hari ini # 3
        $session = $now->lt($siangMulai) ? JP::PAGI : JP::SIANG;       
        $isPresence = Presensi::where('nama_karyawan', $user->name)
                ->where('jenis_presensi', $session->value)
                ->whereDate('tanggal', $now->toDateString())
                ->exists();

        return [
            'presence_start_time' => Carbon::parse($pagiMulai),

            // sesi presensi nya, atau status waktu presensi
            // contohnya BELUM, SESI_PRESENSI dan SELESAI
            'presence_session' => $presensiSession,

            // status presensi atau misalnya
            // MASUK, IJIN, dan dll
            'presence_status' => $statusPresensi,

            // apakah sudah presensi?
            'is_presence' => $isPresence,

            //tipe presensi, pagi atau siang, tapi default nya none
            'presence_type' => $sessionType,

            'timezone' => $timezone,
            'user' => $user,
        ];
    }
}
