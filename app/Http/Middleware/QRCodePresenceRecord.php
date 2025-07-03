<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class QRCodePresenceRecord
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $timezone = 'Asia/Makassar';

        $now = Carbon::now($timezone);

        $pagiMulai = Carbon::createFromTimeString('08:00:00', $timezone);
        $pagiSelesai = Carbon::createFromTimeString('09:00:00', $timezone);

        $siangMulai = Carbon::createFromTimeString('14:00:00', $timezone);
        $siangSelesai = Carbon::createFromTimeString('15:00:00', $timezone);
        
        $sesiPresensi = ($now->between($pagiMulai, $pagiSelesai) || $now->between($siangMulai, $siangSelesai));

        if (!$request->routeIs('presensi.qr')) {
            if ($sesiPresensi) {
                return redirect()->route('presensi.qr');
            }
        }

        return $next($request);
    }
}
