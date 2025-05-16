<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\Movie;

class CacheShowtimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movie:cache-showtimes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get movie showtimes from Google search API';

    public function handle()
    {
        $lastRunCache = storage_path('app/last_run_cache.txt');

        if (file_exists($lastRunCache)) {
            $lastRun = file_get_contents($lastRunCache);
            $lastDate = \Carbon\Carbon::parse($lastRun);

            if ($lastDate->isToday()) {
                $this->info('Showtimes already cached today.');
                return;
            }
        }
        
        $apiKey = config('services.serpapi.key');
        $url = "https://serpapi.com/search.json";

        $response = Http::get($url, [
            'q' => 'AMC Barton Creek Square 14',
            'location' => 'Austin, Texas, United States',
            'hl' => 'en',
            'gl' => 'us',
            'api_key' => $apiKey,
        ]);
    
        if ($response->failed()) {
            $this->error('Failed to get data from SerpAPI.');
            return;
        }
    
        $data = $response->json();
    
        if (!isset($data['showtimes'])) {
            $this->warn('Data showtimes tidak ditemukan.');
            return;
        }
    
        $showtimes = $data['showtimes'];

        if (!$showtimes) {
            $this->info('No showtimes found.');
            return;
        }

        file_put_contents(storage_path('app/showtimes_cache.json'), json_encode($showtimes));
        file_put_contents($lastRunCache, Carbon::now()->toDateTimeString());
        $this->info('Showtimes updated and cached successfully.');
    }
}
