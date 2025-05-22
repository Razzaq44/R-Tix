<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\DeleteExpiredPurchases;
use App\Console\Commands\CacheShowtimes;
use App\Console\Commands\SyncMoviesToDB;
use App\Console\Commands\DeleteExpiredShowtime;

Schedule::command(DeleteExpiredPurchases::class)->timezone('America/Chicago')->everyMinute();
Schedule::command(DeleteExpiredShowtime::class)->timezone('America/Chicago')->daily();
Schedule::command(CacheShowtimes::class)->timezone('America/Chicago')->at('00:01');
Schedule::command(SyncMoviesToDB::class)->timezone('America/Chicago')->at('00:05');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');