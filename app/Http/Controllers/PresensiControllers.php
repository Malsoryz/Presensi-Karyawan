<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PresensiControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $timezone = 'Asia/Makassar';

        $now = Carbon::now($timezone);

        $pagiMulai = Carbon::createFromTimeString('08:00:00', $timezone);
        $pagiSelesai = Carbon::createFromTimeString('09:00:00', $timezone);

        $siangMulai = Carbon::createFromTimeString('14:00:00', $timezone);
        $siangSelesai = Carbon::createFromTimeString('15:00:00', $timezone);

        if ($now->between($pagiMulai, $pagiSelesai) || $now->between($siangMulai, $siangSelesai)) {
            return view('QRPresenceView', [
                'token' => Str::uuid(),
            ]);
        }

        // return view('QRCodePresence', [
        //     'token' => Str::uuid(),
        // ]);

        return redirect()->route('root');
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
        $request->validate($request, [
            'nama_karyawan' => 'required|string|max:255',
            'jenis_presensi' => 'required|in:pagi,siang',
            'tanggal' => 'required|date',
            'ip_address' => 'required',
        ]);

        $timezone = 'Asia/Makassar';

        $now = Carbon::now($timezone);

        $pagiMulai = Carbon::createFromTimeString('08:00:00', $timezone);
        $pagiSelesai = Carbon::createFromTimeString('09:00:00', $timezone);

        $siangMulai = Carbon::createFromTimeString('14:00:00', $timezone);
        $siangSelesai = Carbon::createFromTimeString('15:00:00', $timezone);

        $sesi = null;

        if ($now->gte($pagiMulai) && $now->lte($pagiSelesai)) $sesi = 'pagi';
        if ($now->gte($siangMulai) && $now->lte($siangSelesai)) $sesi = 'siang';

        Presensi::create([
            'nama_karyawan' => auth()->user()->name,
            'jenis_presensi' => $sesi,
            'tanggal' => $now,
            'ip_address' => $request->ip(),
        ]);

        return redirect('/admin/presensi')->with(['success', 'Data berhasil di simpan']);
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
