<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\Presensi;
use App\Models\Config;
use Carbon\Carbon;

class PresensiControllers extends Controller
{
    const SESSION_PAGI = 'pagi';
    const SESSION_SIANG = 'siang';
    const STATUS_MASUK = 'masuk';
    const STATUS_TERLAMBAT = 'terlambat';

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

    private function check(): array
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

        $session = null;
        if ($isPagiSession) $session = self::SESSION_PAGI;
        if ($isSiangSession) $session = self::SESSION_SIANG;

        $isSessionValid = $session !== null;

        $userName = Auth::user()->name;
        $today = $now->toDateString();
        $isPresence = false;

        if ($now->between($pagiMulai, $siangMulai, false)) {
            $isPresence = Presensi::where('nama_karyawan', $userName)
                ->where('jenis_presensi', self::SESSION_PAGI)
                ->whereDate('tanggal', $today)
                ->exists();
        } else {
            $isPresence = Presensi::where('nama_karyawan', $userName)
                ->where('jenis_presensi', self::SESSION_SIANG)
                ->whereDate('tanggal', $today)
                ->exists();
        }

        $isPagiAsli = $now->between($pagiMulai, $pagiSelesai);
        $isSiangAsli = $now->between($siangMulai, $siangSelesai);

        $presenceStatus = null;
        if ($isPagiAsli || $isSiangAsli) {
            $presenceStatus = self::STATUS_MASUK;
        } elseif ((!$isPagiAsli && $isPagiSession) || (!$isSiangAsli && $isSiangSession)) {
            $presenceStatus = self::STATUS_TERLAMBAT;
        }

        return [
            'is_presence' => $isPresence,
            'session' => $session,
            'is_session_valid' => $isSessionValid,
            'presence_status' => $presenceStatus,
            'debug' => [
                // 'pagiMulai' => $pagiMulai,
                // 'pagiSelesai' => $pagiSelesai,
                'siangMulai' => $siangMulai,
                'siangSelesai' => $siangSelesai,
                // 'waktuToleransi' => $waktuToleransi,
                // 'sesiPagiAsli' => $sesiPagiAsli,
                'sesiSiangAsli' => $isSiangSession,
                // 'sesiPagi' => $isPagiSession,
                'sesiSiang' => $isSiangAsli,
                // 'sesi' => $session,
                // 'isSessionValid' => $isSessionValid,
                // 'isPresence' => $isPresence,
                // 'presenceStatus' => $presenceStatus
            ],
        ];
    }
}
