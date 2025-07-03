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

        if (!$request->routeIs('presensi.qr')) {
            if ($now->gte($pagiMulai) && $now->lte($pagiSelesai)) {
                return redirect()->route('presensi.qr');
            } elseif ($now->gte($siangMulai) && $now->lte($siangSelesai)) {
                return redirect()->route('presensi.qr');
            } 
        } else return redirect()->route('root');

        return $next($request);
    }
}
