<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class PresensiControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $timezone = 'Asia/Makassar';

        // $now = Carbon::now($timezone);

        // $pagiMulai = Carbon::createFromTimeString('08:00:00', $timezone);
        // $pagiSelesai = Carbon::createFromTimeString('09:00:00', $timezone);

        // $siangMulai = Carbon::createFromTimeString('14:00:00', $timezone);
        // $siangSelesai = Carbon::createFromTimeString('15:00:00', $timezone);

        // if ($now->between($pagiMulai, $pagiSelesai) || $now->between($siangMulai, $siangSelesai)) {
        //     $token = Str::uuid();
        //     Cache::put('token_'.$token, true, Carbon::now()->addMinutes(1));
        //     return view('QRPresenceView', [
        //         'token' => $token,
        //     ]);
        // }

        $token = Str::uuid();
        Cache::put('token_'.$token, true, Carbon::now()->addMinutes(1));
        return view('QRPresenceView', [
            'token' => $token,
        ]);

        // return redirect()->route('root');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $token)
    {
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'jenis_presensi' => 'required|in:pagi,siang',
            'tanggal' => 'required|date',
            'ip_address' => 'required',
        ]);

        if (!Cache::has('token_'.$token)) {
            return response()->json(['error' => 'Token tidak valid atau kadaluarsa'], 403);
        }

        $timezone = 'Asia/Makassar';

        $now = Carbon::now($timezone);

        $pagiMulai = Carbon::createFromTimeString('08:00:00', $timezone);
        $pagiSelesai = Carbon::createFromTimeString('09:00:00', $timezone);

        $siangMulai = Carbon::createFromTimeString('14:00:00', $timezone);
        $siangSelesai = Carbon::createFromTimeString('15:00:00', $timezone);

        $sesi = null;

        if ($now->between($pagiMulai, $pagiSelesai)) $sesi = 'pagi';
        if ($now->between($siangMulai, $siangSelesai)) $sesi = 'siang';

        Presensi::create([
            'nama_karyawan' => auth()->user()->name,
            'jenis_presensi' => $sesi,
            'tanggal' => $now,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('root')->with(['success', 'Data berhasil di simpan']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
