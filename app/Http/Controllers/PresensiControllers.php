<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\Presensi;

class PresensiControllers extends Controller
{
    public function index()
    {
        // $getToday = Presensi::where('nama_karyawan', Auth::user()->name)
        //     ->where('jenis_presensi', 'pagi')
        //     ->whereDate('tanggal', now('Asia/Makassar')->toDateString())
        //     ->exists();
        // if ($getToday) {
        //     return redirect()->route('presensi.scanned')->with('info', 'Anda sudah melakukan presensi hari ini.');
        // }

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

        return redirect()->route('presensi.scanned')->with('success', 'Presensi berhasil dilakukan.');
    }

    public function scanCheck()
    {
        $status = Presensi::where('nama_karyawan', Auth::user()->name)
            ->where('jenis_presensi', 'pagi')
            ->whereDate('tanggal', now('Asia/Makassar')->toDateString())
            ->exists();

        return response()->json([
            'is_presence' => $status,
        ]);
    }

    public function scanned()
    {
        // $getToday = Presensi::where('nama_karyawan', Auth::user()->name)
        //     ->where('jenis_presensi', 'pagi')
        //     ->whereDate('tanggal', now('Asia/Makassar')->toDateString())
        //     ->exists();

        // if (!$getToday) {
        //     return redirect()->route('presensi.index')->with('error', 'Anda belum melakukan presensi hari ini.');
        // }

        return view('Presensi.scanned');
    }
}
