<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\DeleteExpiredPurchases;
use App\Console\Commands\CacheShowtimes;
use App\Console\Commands\SyncMoviesToDB;
use App\Console\Commands\DeleteExpiredShowtime;

Schedule::command(DeleteExpiredPurchases::class)->timezone('Asia/Jakarta')->everyMinute();
Schedule::command(DeleteExpiredShowtime::class)->timezone('Asia/Jakarta')->everyThirtyMinutes();
Schedule::command(CacheShowtimes::class)->timezone('Asia/Jakarta')->at('3:01');
Schedule::command(SyncMoviesToDB::class)->timezone('Asia/Jakarta')->at('3:30');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');