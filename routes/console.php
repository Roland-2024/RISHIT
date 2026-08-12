<?php

use App\Application\Commerce\ExpireReservations;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('orders:expire-reservations', function (ExpireReservations $expire): void {
    $this->info(sprintf('Expired %d reservation(s).', $expire()));
})->purpose('Release fixed-price reservations whose payment deadline passed');

Schedule::command('orders:expire-reservations')
    ->everyMinute()
    ->onSuccess(fn () => Cache::forget('orders:reservation-cleanup-failures'))
    ->onFailure(function (): void {
        if (Cache::increment('orders:reservation-cleanup-failures') === 3) {
            Log::critical('Order reservation cleanup failed three consecutive times.');
        }
    });
