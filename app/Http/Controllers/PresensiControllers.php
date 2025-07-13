<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\Presensi;
use App\Models\Config;
use Carbon\Carbon;

use App\Enums\StatusPresensi as SP;

enum SesiPresensi: string {
    case INVALID = 'invalid';
    case PAGI = 'pagi';
    case SIANG = 'siang';
}

class PresensiControllers extends Controller
{
    public function index()
    {
        // $presensi = $this->check();

        // $collection = Presensi::getTotal()->limit(3)->get();

        // $token = Str::uuid();
        // $name = Auth::user()->name;
        // Cache::put("token_{$name}_{$token}", true, now()->addMinutes(1));
        // return view('Presensi.index', [
        //     'name' => $name,
        //     'token' => $token,
        //     'collection' => $collection,
        //     'status' => $presensi['presence_status']->value,
        // ]);

        // // separator

        $presensi = $this->check();
        $now = now($presensi['timezone']);

        $name = Auth::user()->name;
        $topThree = Presensi::getTotal()->limit(3)->get();

        if (!$presensi['is_presence']) {
            $token = Str::uuid();
            Cache::put("token_{$name}_{$token}", true, now()->addMinutes(1));
            return view('Presensi.index', [
                'name' => $name,
                'token' => $token,
                'topThreePresence' => $topThree, // ambil 3 data presensi teratas
                'status' => SP::BELUM->value,
            ]);
        }

        $status = $presensi['presence_status'];
        $presenceDateTime = Presensi::where('nama_karyawan', $name)
            ->where('jenis_presensi', $presensi['presence_session']->value)
            ->whereDate('tanggal', $now->toDateString())
            ->first()->value('tanggal');
        $presenceTime = Carbon::parse($presenceDateTime)->format('H:i:s');

        return view('Presensi.info', [
            'status' => $status->value,
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

        if (!$presensi['is_presence_session']) {
            return redirect()->route('presensi.index')->with('error', 'Sesi presensi tidak valid!');
        }

        if ($presensi['presence_session'] !== SesiPresensi::INVALID) {
            Presensi::create([
                'nama_karyawan' => $name,
                'jenis_presensi' => $presensi['presence_session']->value,
                'status' => $presensi['presence_status'],
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('presensi.info', ['status' => 'telah presensi']);
    }

    
    public function info(Request $request)
    {
        if (!$request->has('presence')) {
            return redirect()->route('presensi.index');
        }

        $presence = $request->get('presence');
        $checked = $this->check();
        
        if (!$presence && $checked['is_session_valid']) {
            return redirect()->route('presensi.index');
        }
        
        if (!$checked['is_presence'] && $checked['is_session_valid']) {
            return redirect()->route('presensi.index');
        }
        
        if ($presence != $checked['is_presence']) {
            return redirect()->route('presensi.index');
        }

        $message = ($presence) ? 'telah presensi' : 'telat presensi';

        return view('Presensi.info', [
            'status' => $message,
            // 'debug' => $checked['debug'],
        ]);
    }

    public function scanCheck()
    {
        $checked = $this->check();

        return response()->json([
            'is_presence' => $checked['is_presence'],
            'is_time_valid' => $checked['is_session_valid'],
        ]);
    }
    
    public function test()
    {
        return var_dump($this->check());
    }

    private function check(): array
    {
        // user
        $userName = Auth::user()->name;

        // dapatkan waktu sekarang dan timezone
        $timezone = Config::get('timezone', 'Asia/Makassar');
        $now = now($timezone);
        $today = $now->toDateString();

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
        $isPresensiSession = $now->between($pagiMulai, $pulangKerja); // satu hari kerja. untuk mengetahui apakah masih bisa presensi # 1

        $isAfterPagi = $now->between($pagiSelesai->copy()->addMinutes($toleransi), $siangMulai);
        $isAfterSiang = $now->between($siangSelesai->copy()->addMinutes($toleransi), $pulangKerja);

        $statusPresensi = SP::BELUM; // Status Presensi # 2 perlu diambil ->value
        if ($isPresensiSession) {
            $statusPresensi = SP::MASUK;
        }
        if ($isAfterPagi || $isAfterSiang) {
            $statusPresensi = SP::TERLAMBAT;
        }
        if ($now->gt($pulangKerja)) {
            $statusPresensi = SP::TIDAK_MASUK;
        }
        
        $session = SesiPresensi::INVALID; // mendapatkan sesi sekarang, default value null # 4
        if ($now->between($pagiMulai, $siangMulai)) $session = SesiPresensi::PAGI;
        if ($now->between($siangMulai, $pulangKerja)) $session = SesiPresensi::SIANG;

        // cek apakah sudah presensi hari ini # 3
        $isPresence = Presensi::where('nama_karyawan', $userName)
                ->where('jenis_presensi', $session->value)
                ->whereDate('tanggal', $today)
                ->exists();

        return [
            'is_presence_session' => $isPresensiSession,
            'presence_status' => $statusPresensi,
            'is_presence' => $isPresence,
            'presence_session' => $session,
            'timezone' => $timezone,
        ];
    }
}
