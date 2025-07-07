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
    public function index(Request $request)
    {
        $status = $request->get('status');
        $token = Str::uuid();
        $name = Auth::user()->name;
        Cache::put('token_' . $name . '_' . $token, true, now()->addMinutes(1));
        return view('Presensi.index', [
            'name' => $name,
            'token' => $token,
            'status' => $status
        ]);
    }

    public function store(Request $request, string $name, string $token)
    {
        if (!Cache::has('token_' . $name . '_' . $token)) {
            return redirect()->route('presensi.index')->with('error', 'Token tidak valid atau sudah kadaluarsa.');
        }

        Cache::forget('token_' . $name . '_' . $token);

        $timezone = 'Asia/Makassar';
        $now = now($timezone);
        $pagiMulai = Carbon::createFromTimeString(Config::get('presensi_pagi_mulai'), $timezone);
        $pagiSelesai = Carbon::createFromTimeString(Config::get('presensi_pagi_selesai'), $timezone);
        $siangMulai = Carbon::createFromTimeString(Config::get('presensi_siang_mulai'), $timezone);
        $siangSelesai = Carbon::createFromTimeString(Config::get('presensi_siang_selesai'), $timezone);

        $sesiPagi = $now->between($pagiMulai, $pagiSelesai);
        $sesiSiang = $now->between($siangMulai, $siangSelesai);

        $sesi = null;
        if ($sesiPagi) $sesi = 'pagi';
        if ($sesiSiang) $sesi = 'siang';

        $isSesiValid = $sesi !== null;

        // Presensi::create([
        //     'nama_karyawan' => $name,
        //     'jenis_presensi' => 'pagi',
        //     'ip_address' => $request->ip(),
        // ]);

        if (!$isSesiValid) {
            return redirect()->route('presensi.index', ['status' => 'late'])->with('error', 'Sesi presensi tidak valid!');
        }

        if ($sesi === 'pagi' || $sesi === 'siang') {
            Presensi::create([
                'nama_karyawan' => $name,
                'jenis_presensi' => $sesi,
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('presensi.index', ['status' => 'presence'])->with('success', 'Presensi berhasil dilakukan.');
    }

    public function scanCheck()
    {
        $timezone = 'Asia/Makassar';
        $now = now($timezone);
        $pagiMulai = Carbon::createFromTimeString(Config::get('presensi_pagi_mulai'), $timezone);
        $pagiSelesai = Carbon::createFromTimeString(Config::get('presensi_pagi_selesai'), $timezone);
        $siangMulai = Carbon::createFromTimeString(Config::get('presensi_siang_mulai'), $timezone);
        $siangSelesai = Carbon::createFromTimeString(Config::get('presensi_siang_selesai'), $timezone);

        $sesiPagi = $now->between($pagiMulai, $pagiSelesai);
        $sesiSiang = $now->between($siangMulai, $siangSelesai);

        $sesi = null;
        if ($sesiPagi) $sesi = 'pagi';
        if ($sesiSiang) $sesi = 'siang';

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

        return response()->json([
            'is_presence' => $status,
            'is_time_valid' => $sesi !== null,
            'session' => $sesi,
        ]);
    }
}
