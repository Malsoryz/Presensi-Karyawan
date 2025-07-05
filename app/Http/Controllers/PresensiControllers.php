<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\Presensi;
use Carbon\Carbon;

class PresensiControllers extends Controller
{
    public function index()
    {
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

        Presensi::create([
            'nama_karyawan' => $name,
            'jenis_presensi' => 'pagi',
            'tanggal' => now('Asia/Makassar'),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('presensi.presence')->with('success', 'Presensi berhasil dilakukan.');
    }

    public function scanCheck()
    {
        $timezone = 'Asia/Makassar';
        $pagiMulai = Carbon::createFromTimeString('08:00:00');
        $pagiSelesai = Carbon::createFromTimeString('09:00:00');
        $siangMulai = Carbon::createFromTimeString('14:00:00');
        $siangSelesai = Carbon::createFromTimeString('15:00:00');

        $sesiPagi = now()->between($pagiMulai, $pagiSelesai);
        $sesiSiang = now()->between($siangMulai, $siangSelesai);

        $sesi = null;
        if ($sesiPagi) $sesi = 'pagi';
        if ($sesiSiang) $sesi = 'siang';

        $isSesiValid = $sesi !== null;

        $status = Presensi::where('nama_karyawan', Auth::user()->name)
            ->where('jenis_presensi', 'pagi')
            ->whereDate('tanggal', now('Asia/Makassar')->toDateString())
            ->exists();

        return response()->json([
            'is_presence' => $status,
        ]);
    }

    public function presence()
    {
        return view('Presensi.presence');
    }
}
