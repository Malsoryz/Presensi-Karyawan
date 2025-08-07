<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Presensi;
use App\Models\Config;
use App\Models\Background;
use App\Support\Inspire\Motivation;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

use Carbon\Carbon;
use App\Enums\Presensi\StatusPresensi;
use App\Enums\Presensi\SesiPresensi;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ApiController extends Controller
{
    public function presenceQrCode()
    {
        $token = Str::uuid();
        Cache::put("token_{$token}", true, now()->addMinutes(1));
        Cookie::queue('browser_token', $token, 1);
        $svg = QrCode::size(256)->generate(route('presensi.store', ['token' => $token]));
        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml');
            // agar yang dikembalikan adalah svg
    }

    public function presencesData(Request $request)
    {
        $name = $request->query('name');
        $accumulation = Presensi::accumulatedUser($name);
        return response()->json([
            'today_presences' => Presensi::today()
                ->limit(5)
                ->get()
                ->toArray(),
            'user_accumulation' => [
                'masuk' => (int) $accumulation ? $accumulation->total_masuk : 0,
                'terlambat' => (int) $accumulation ? $accumulation->total_terlambat : 0,
                'ijin' => (int) $accumulation ? $accumulation->total_ijin : 0,
                'sakit' => (int) $accumulation ? $accumulation->total_sakit : 0,
                'tidak_masuk' => (int) $accumulation ? $accumulation->total_tidak_masuk : 0,
            ],
        ]);
    }

    public function authUserData(Request $request)
    {
        $user = null;

        if ($request->hasCookie('browser_token')) {
            $cookieToken = $request->cookie('browser_token');
            if (Cache::has("scanned_{$cookieToken}")) {
                $userId = Cache::get("scanned_{$cookieToken}");
                $user = User::find($userId);
                Cookie::queue('user', $userId, 43200);
                Cookie::queue(Cookie::forget('browser_token'));
            }
        }

        if ($request->hasCookie('user')) {
            $userId = $request->cookie('user');
            $user = User::find($userId);
        }

        if (Auth::check()) {
            $user = Auth::user();
            if (!$request->hasCookie('user')) {
                Cookie::queue('user', $user->id, 43200);
                Cookie::queue(Cookie::forget('browser_token'));
            }
        }

        return response()->json([
            'message' => (bool) $user ? "user {$user->name} menggunakan token" : 'Nothing happen.',
            'is_detected' => (bool) $user,
            'is_login' => Auth::check(),
            'user' => (bool) $user ? [
                'name' => $user->name,
            ] : null,
        ]);
    }

    public function checkApproval(Request $request)
    {
        $id = $request->query('id');
        $user = User::find($id);
        $isApproved = (bool) $user->status_approved;
        return response()->json([
            'is_approved' => $isApproved,
        ]);
    }

    public function motivation()
    {
        return response()->json(Motivation::quote());
    }

    public function getDatetime()
    {
        $now = now(Config::timezone());
        return response()->json($now->locale('id')->isoFormat('dddd, DD MMMM'));
    }

    public function presencesStatus()
    {
        extract(Config::presencesTime());

        $now = now($timezone);

        $isAfterPagi = $now->between($pagiSelesai->copy()->addMinutes($toleransi), $siangMulai);
        $isAfterSiang = $now->between($siangSelesai->copy()->addMinutes($toleransi), $pulangKerja);

        $sesiPagi = $now->between($pagiMulai, $pagiSelesai->copy()->addMinutes($toleransi));
        $sesiSiang = $now->between($siangMulai, $siangSelesai->copy()->addMinutes($toleransi));

        $presencesStatus = match (true) {
            $sesiPagi || $sesiSiang => [
                'status' => 'Ontime',
                'class' => 'text-green-600 dark:text-green-400',
            ], //StatusPresensi::Masuk,
            $isAfterPagi || $isAfterSiang => [
                'status' => 'Terlambat',
                'class' => 'text-yellow-600 dark:text-yellow-400',
            ], //StatusPresensi::Terlambat,
            $now->gt($pulangKerja) => [
                'status' => 'Tidak masuk',
                'class' => 'text-red-600 dark:text-red-400',
            ], //StatusPresensi::TidakMasuk,
            default => null,
        };

        return response()->json($presencesStatus);
    }

    public function backgrounds()
    {
        $background = Background::randomImage();
        $count = Background::countImage();
        return response()->json($background ? [
            'background' => $background,
            'count' => $count,
        ] : null);
    }
}
