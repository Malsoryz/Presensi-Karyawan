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
    case PAGI = 'pagi';
    case SIANG = 'siang';
}

class PresensiControllers extends Controller
{
    public function index()
    {
        $checked = $this->check();

        if ($checked['is_presence']) {
            return redirect()->route('presensi.info', ['presence' => true]);
        } elseif (!$checked['is_presence'] && !$checked['is_session_valid']) {
            return redirect()->route('presensi.info', ['presence' => false]);
        }

        $collection = Presensi::getTotal()->limit(3)->get();

        $token = Str::uuid();
        $name = Auth::user()->name;
        Cache::put('token_' . $name . '_' . $token, true, now()->addMinutes(1));
        return view('Presensi.index', [
            'name' => $name,
            'token' => $token,
            'collection' => $collection,
            'status' => $checked['presence_status'],
        ]);
    }

    public function store(Request $request, string $name, string $token)
    {
        if (!Cache::has('token_' . $name . '_' . $token)) {
            return redirect()->route('presensi.index')->with('error', 'Token tidak valid atau sudah kadaluarsa.');
        }

        Cache::forget('token_' . $name . '_' . $token);

        if ($name != Auth::user()->name) {
            return redirect()->route('presensi.index')->with('error', 'Tidak bisa melakukan presensi untuk orang lain.');
        }

        $checked = $this->check();

        if (!$checked['is_session_valid']) {
            return redirect()->route('presensi.index')->with('error', 'Sesi presensi tidak valid!');
        }

        if ($checked['session'] === 'pagi' || $checked['session'] === 'siang') {
            Presensi::create([
                'nama_karyawan' => $name,
                'jenis_presensi' => $checked['session'],
                'status' => $checked['presence_status'],
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('presensi.info', ['status' => 'telah presensi']);
    }

    public function scanCheck()
    {
        $checked = $this->check();

        return response()->json([
            'is_presence' => $checked['is_presence'],
            'is_time_valid' => $checked['is_session_valid'],
        ]);
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

        // waktu presensi
        $isPagiPresensiSession = $now->between($pagiMulai, $pagiSelesai->copy()->addMinutes($toleransi)); // pagi
        $isSiangPresensiSession = $now->between($siangMulai, $siangSelesai->copy()->addMinutes($toleransi)); // siang

        $isPresensiSession = ($isPagiPresensiSession || $isSiangPresensiSession); // untuk mengetahui apakah masih bisa presensi # 1

        $isAfterPagi = $now->between($pagiSelesai->copy()->addMinutes($toleransi), $siangMulai);
        $isAfterSiang = $now->gt($siangSelesai) || $now->lt($pagiMulai);

        $statusPresensi = SP::TIDAK_MASUK; // Status Presensi # 2 perlu diambil ->value
        if ($isPagiPresensiSession || $isSiangPresensiSession) {
            $statusPresensi = SP::MASUK;
        }
        if ($isAfterPagi || $isAfterSiang) {
            $statusPresensi = SP::TERLAMBAT;
        }

        $isPresence = false; // apakah sudah presensi, default value nya false # 3
        if ($now->between($pagiMulai, $siangMulai, false)) {
            $isPresence = Presensi::where('nama_karyawan', $userName)
                ->where('jenis_presensi', SesiPresensi::PAGI->value)
                ->whereDate('tanggal', $today)
                ->exists();
        } else {
            $isPresence = Presensi::where('nama_karyawan', $userName)
                ->where('jenis_presensi', SesiPresensi::SIANG->value)
                ->whereDate('tanggal', $today)
                ->exists();
        }

        $session = null; // mendapatkan sesi sekarang, default value null # 4
        if ($isPagiPresensiSession) $session = SesiPresensi::PAGI->value;
        if ($isSiangPresensiSession) $session = SesiPresensi::SIANG->value;

        return [
            'is_presence_session' => $isPresensiSession,
            'status_presensi' => $statusPresensi->value,
            'is_presence' => $isPresence,
            'presence_session' => $session,
        ];
    }
}
