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
    public function index()
    {
        $checked = $this->check();

        if ($checked['status']) {
            return redirect()->route('presensi.info', ['status' => 'telah presensi']);
        } elseif (!$checked['status'] && !$checked['is_session_valid']) {
            return redirect()->route('presensi.info', ['status' => 'telat presensi']);
        }

        $token = Str::uuid();
        $name = Auth::user()->name;
        Cache::put('token_' . $name . '_' . $token, true, now()->addMinutes(1));
        return view('Presensi.index', [
            'name' => $name,
            'token' => $token,
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
                'status' => 'masuk',
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('presensi.info', ['status' => 'telah presensi']);
    }

    public function scanCheck()
    {
        $checked = $this->check();

        return response()->json([
            'is_presence' => $checked['status'],
            'is_time_valid' => $checked['is_session_valid'],
        ]);
    }

    public function info(Request $request)
    {
        $status = $request->get('status');

        if ($status === null) {
            return redirect()->route('presensi.index');
        }

        return view('Presensi.info', ['status' => $status]);
    }

    private function check(): array
    {
        $timezone = Config::get('timezone', 'Asia/Makassar');
        $now = now($timezone);
        $pagiMulai = Carbon::createFromTimeString(
            Config::get('presensi_pagi_mulai', '08:00:00'), $timezone
        );
        $pagiSelesai = Carbon::createFromTimeString(
            Config::get('presensi_pagi_selesai', '09:00:00'), $timezone
        );
        $siangMulai = Carbon::createFromTimeString(
            Config::get('presensi_siang_mulai', '14:00:00'), $timezone
        );
        $siangSelesai = Carbon::createFromTimeString(
            Config::get('presensi_siang_selesai', '15:00:00'), $timezone
        );
        $waktuToleransi = Config::get('toleransi_presensi', 0);

        $sesiPagi = $now->between($pagiMulai, $pagiSelesai->addMinute((int) $waktuToleransi));
        $sesiSiang = $now->between($siangMulai, $siangSelesai->addMinute((int) $waktuToleransi));

        $sesi = null;
        if ($sesiPagi) $sesi = 'pagi';
        if ($sesiSiang) $sesi = 'siang';

        $isSesiValid = $sesi !== null;

        $status;

        if ($now->between($pagiMulai, $siangMulai, false)) {
            $status = Presensi::where('nama_karyawan', Auth::user()->name)
                ->where('jenis_presensi', 'pagi')
                ->whereDate('tanggal', $now->toDateString())
                ->exists();
        } else {
            $status = Presensi::where('nama_karyawan', Auth::user()->name)
                ->where('jenis_presensi', 'siang')
                ->whereDate('tanggal', $now->toDateString())
                ->exists();
        }

        return [
            'status' => $status,
            'session' => $sesi,
            'is_session_valid' => $isSesiValid
        ];
    }
}
