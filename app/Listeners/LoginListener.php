<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use App\Models\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class LoginListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        $now = now(Config::timezone());
        $tomorrow = Carbon::tomorrow(Config::timezone());
        $minuteUntilTomorrow = $now->diffInMinutes($tomorrow->startOfDay());

        if (request()->hasCookie('update_cookie')) {
            Log::info("{$user->name} hari ini sudah memperbarui Cookie");
        } else {
            Cookie::queue('user', $user->id, 43200);
            Cookie::queue('update_cookie', true, $minuteUntilTomorrow);

            Log::info("{$user->name} memperbarui Cookie");
        }

    }
}
