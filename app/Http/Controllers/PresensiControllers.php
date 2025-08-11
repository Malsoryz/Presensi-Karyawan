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
use App\Models\Background;
use App\Enums\Presensi\StatusPresensi;
use App\Enums\Presensi\JenisPresensi;
use App\Enums\Presensi\SesiPresensi;
use App\Enums\User\Role;

class PresensiControllers extends Controller
{
    public function index(Request $request)
    {
        $now = now(Config::timezone());
        $endOfDay = Carbon::tomorrow()->startOfDay();
        $minutesUntilMidnight = $now->diffInMinutes($endOfDay);

        $background = $request->cookie('todayWallpaper') ?? Background::randomImage();

        if (!$request->hasCookie('todayWallpaper')) {
            Cookie::queue('todayWalpaper', $background, $minutesUntilMidnight);
        }

        $data = [ // default
            'message' => "Nothing have to say.",
            'isPresenceAllowed' => true,
        ];
        
        if (Auth::check() || $request->hasCookie('user')) {
            $presensi = (object) $this->check();
            $now = now($presensi->timezone);

            // diluar jam presensi
            if (!$presensi->presenceSession->isSession()) {
                $data = [
                    'message' => match (true) {
                        $now->lt($presensi->presenceStartTime) => "Presensi belum dimulai.",
                        $now->gt($presensi->presenceEndTime) => "Presensi telah berakhir",
                        default => "how?",
                    },
                    'isPresenceAllowed' => false,
                    'presenceStartAt' => $now->lt($presensi->presenceStartTime) ? $presensi->presenceStartTime : null,
                ];
            }

            // jika hari minggu atau hari libur nasional
            if ($now->isSunday() || HariLibur::isHoliday()) {
                $holiday = HariLibur::todayHoliday();
                $data = [
                    'message' => match (true) {
                        HariLibur::isHoliday() => "Hari ini adalah hari libur nasional!",
                        $now->isSunday() => "Sayangnya hari ini, hari minggu.",
                        default => "how?",
                    },
                    'isPresenceAllowed' => false,
                    'todayHoliday' => $holiday,
                ];
            }
        }

        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('filament.admin.pages.dashboard');
        }

        // default view atau untuk yang belum login
        return view('presensi', $data);
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
        if ($user->isAdmin()) {
            return redirect()
                ->route('presensi.index')
                ->with('info', "{$user->name} adalah Admin");
        }

        if ($presensi->isPresence) {
            return redirect()
                ->route('presensi.index')
                ->with('info', "hari ini $user->name telah melakukan presensi");
        }

        if ($presensi->presenceType !== null && $presensi->presenceStatus !== null) {
            $user->presensis()->create([
                'nama_karyawan' => $user->name,
                'jenis_presensi' => $presensi->presenceType->value,
                'status' => $presensi->presenceStatus->value,
                'ip_address' => $request->ip(),
            ]);
            Cookie::queue('user', $user->id, 43200);
        }

        Cache::put("scanned_{$token}", $user->id, now()->addMinutes(1));
        Cache::forget("token_{$token}");

        return redirect()
            ->route('presensi.index')
            ->with('info', "$user->name melakukan presensi");
    }

    public function test()
    {
        return dd(Presensi::limit(5)->get());
        // return dd($this->check());
    }

    private function check(): array
    {
        // user
        // agar jika dalam keadaan belum login
        // data user didapat dari cookie
        $userId = request()->cookie('user');
        $user = Auth::user() ?? User::find($userId);

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

        $sesiPagi = $now->between($pagiMulai, $pagiSelesai->copy()->addMinutes($toleransi));
        $sesiSiang = $now->between($siangMulai, $siangSelesai->copy()->addMinutes($toleransi));

        // Status Presensi # 2 perlu diambil ->value
        $statusPresensi = match (true) {
            $sesiPagi || $sesiSiang => StatusPresensi::Masuk,
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
        $isPresence = Presensi::where('nama_karyawan', $user->name ?? '')
                ->where('jenis_presensi', $session->value)
                ->whereDate('tanggal', $now->toDateString())
                ->exists();

        return [
            // Carbon
            'presenceStartTime' => $pagiMulai,
            'presenceEndTime' => $pulangKerja,

            // sesi presensi nya, atau status waktu presensi
            // enum SesiPresensi
            'presenceSession' => $presensiSession,

            // status presensi atau misalnya
            // enum StatusPresensi
            'presenceStatus' => $statusPresensi,

            // apakah sudah presensi?
            // bool
            'isPresence' => $isPresence,

            //tipe presensi, pagi atau siang, tapi default nya none
            // enum JenisPresensi
            'presenceType' => $sessionType,

            // string
            'timezone' => $timezone,
        ];
    }
}
