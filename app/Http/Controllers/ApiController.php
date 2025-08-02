<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Presensi;
use App\Support\Inspire\Motivation;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

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
            'user_accumulation' => (bool) $accumulation ? [
                'masuk' => (int) $accumulation->total_masuk,
                'terlambat' => (int) $accumulation->total_terlambat,
                'ijin' => (int) $accumulation->total_ijin,
                'sakit' => (int) $accumulation->total_sakit,
                'tidak_masuk' => (int) $accumulation->total_tidak_masuk,
            ] : null,
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
}
