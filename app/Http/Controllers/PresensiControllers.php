<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Carbon\Carbon;

use App\Models\Presensi;
use App\Models\Config;
use App\Models\HariLibur;
use App\Models\User;
use App\Enums\Presensi\StatusPresensi;
use App\Enums\Presensi\JenisPresensi;
use App\Enums\Presensi\SesiPresensi;

class PresensiControllers extends Controller
{
    public function index()
    {
        return view('presensi');
    }

    public function store(Request $request, string $token)
    {
        if (!Cache::has("token_{$token}")) {
            return redirect()
                ->route('presensi.index')
                ->with('info', 'token tidak valid');
        }

        $presensi = (object) $this->check();

        if (!$presensi->presenceSession->isSession()) {
            return redirect()
                ->route('presensi.index')
                ->with('info', 'bukan waktu presensi');
        }

        $user = Auth::user();
        if ($presensi->presenceType !== null && $presensi->presenceStatus !== null) {
            $user->presensis()->create([
                'nama_karyawan' => $user->name,
                'jenis_presensi' => $presensi->presenceType->value,
                'status' => $presensi->presenceStatus->value,
                'ip_address' => $request->ip(),
            ]);
        }

        Cache::put("scanned_{$token}", $user, now()->addMinutes(1));
        Cache::forget("token_{$token}");

        return redirect()
            ->route('presensi.index')
            ->with('info', 'telah presensi');
    }

    public function getQr()
    {
        $token = Str::uuid();
        Cache::put("token_{$token}", true, now()->addMinutes(1));
        Cookie::queue('browser_token', $token, 1);
        $svg = QrCode::size(256)->generate(route('presensi.store', ['token' => $token]));
        return response($svg, 200)->header('Content-Type', 'image/svg+xml');
    }

    public function getUser(Request $request)
    {
        // ambil jika ada
        if ($request->hasCookie('user')) {
            $userId = $request->cookie('user');
            $user = User::find($userId);
            return response()->json([
                'message' => 'user '.$user->name.' menggunakan token',
                'is_detected' => true,
            ]);
        }

        // buat jika tidak ada
        $cookieToken = $request->cookie('browser_token');
        if (Cache::has("scanned_{$cookieToken}")) {
            $user = Cache::get("scanned_{$cookieToken}");
            Cookie::queue('user', $user->id, 43200);
            Cookie::queue(Cookie::forget('token_browser'));
            return response()->json([
                'message' => 'user '.$user->name.' menggunakan token',
                'is_detected' => true,
            ]);
        }

        // jika browser token sama sekali tidak ada
        return response()->json([
            'message' => 'Nothing happen.',
            'is_detected' => false,
        ]);
    }

    public function checkCookie(Request $request)
    {
        return var_dump($request->cookie('user'));
    }

    private function check(): array
    {
        // user
        $user = Auth::user();

        // dekonstruksi array dari model
        extract(Config::presencesTime());

        $now = now($timezone);

        // satu hari kerja. untuk mengetahui apakah masih bisa presensi # 1
        $presensiSession = match (true) {
            $now->between($pagiMulai, $pulangKerja) => SesiPresensi::SesiPresensi,
            $now->gt($pulangKerja) => SesiPresensi::None,
            default => SesiPresensi::None,
        };

        $isAfterPagi = $now->between($pagiSelesai->copy()->addMinutes($toleransi), $siangMulai);
        $isAfterSiang = $now->between($siangSelesai->copy()->addMinutes($toleransi), $pulangKerja);

        // Status Presensi # 2 perlu diambil ->value
        $statusPresensi = match (true) {
            $presensiSession => StatusPresensi::Masuk,
            $isAfterPagi || $isAfterSiang => StatusPresensi::Terlambat,
            $now->gt($pulangKerja) => StatusPresensi::TidakMasuk,
            default => null,
        };
        
        // mendapatkan sesi sekarang, default value null # 4
        $sessionType = match (true) {
            $now->between($pagiMulai, $siangMulai) => JenisPresensi::Pagi,
            $now->between($siangMulai, $pulangKerja) => JenisPresensi::Siang,
            default => null,
        };

        // cek apakah sudah presensi hari ini # 3
        $session = $now->lt($siangMulai) ? JenisPresensi::Pagi : JenisPresensi::Siang;       
        $isPresence = Presensi::where('nama_karyawan', $user->name)
                ->where('jenis_presensi', $session->value)
                ->whereDate('tanggal', $now->toDateString())
                ->exists();

        return [
            'presenceStartTime' => Carbon::parse($pagiMulai),

            // sesi presensi nya, atau status waktu presensi
            // contohnya BELUM, SESI_PRESENSI dan SELESAI
            'presenceSession' => $presensiSession,

            // status presensi atau misalnya
            // MASUK, IJIN, dan dll
            'presenceStatus' => $statusPresensi,

            // apakah sudah presensi?
            'isPresence' => $isPresence,

            //tipe presensi, pagi atau siang, tapi default nya none
            'presenceType' => $sessionType,

            'timezone' => $timezone,
            'user' => $user,
        ];
    }
}
