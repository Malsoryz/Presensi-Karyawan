<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

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
        //     return view('QRCodePresence.index', [
        //         'token' => $token,
        //     ]);
        // }

        if (Auth::check()) {
            $token = Str::uuid();
            Cache::put('token_'.$token, true, Carbon::now()->addMinutes(1));
            return view('QRCodePresence.index', [
                'name' => auth()->user()->name,
                'token' => $token,
            ]);
        }

        return redirect()->route('filament.admin.auth.login');

        // return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $name, string $token)
    {
        if (!Cache::has('token_'.$token)) {
            return response()->json(['error' => 'Token tidak valid atau kadaluarsa'], 403);
        }

        Cache::forget('token_'.$token);

        $timezone = 'Asia/Makassar';

        $now = Carbon::now($timezone);

        // $pagiMulai = Carbon::createFromTimeString('08:00:00', $timezone);
        // $pagiSelesai = Carbon::createFromTimeString('09:00:00', $timezone);

        // $siangMulai = Carbon::createFromTimeString('14:00:00', $timezone);
        // $siangSelesai = Carbon::createFromTimeString('15:00:00', $timezone);

        // $sesi = null;

        // if ($now->between($pagiMulai, $pagiSelesai)) $sesi = 'pagi';
        // if ($now->between($siangMulai, $siangSelesai)) $sesi = 'siang';

        // $validatedRequest = $request->validate([
        //     'nama_karyawan' => 'required|string|max:255',
        //     'jenis_presensi' => 'required|string|in:pagi,siang',
        //     'tanggal' => 'required|date',
        //     'ip_address' => 'required|string',
        // ]);

        if ($name != auth()->user()->name) {
            return redirect()->route('presensi.qr'); // temp
        }

        Presensi::create([
            'nama_karyawan' => $name,
            'jenis_presensi' => 'pagi',
            'tanggal' => $now,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('filament.admin.resources.presensi.index');
    }
}
