<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\Showtime;
use App\Models\ShowingSeats;
use App\Models\Seat;
use Carbon\Carbon;

class SyncMoviesToDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:movies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync movies and showtimes data to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cacheFilePath = storage_path('app/showtimes_cache.json');

        if (!file_exists($cacheFilePath)) {
            $this->error('Cache file not found.');
            return;
        }

        $showtimes = json_decode(file_get_contents($cacheFilePath), true);

        if (empty($showtimes)) {
            $this->info('No showtimes found in the cache file.');
            return;
        }

        $this->deletePastShowtimes();

        $showDate = Carbon::today();
        foreach ($showtimes as $dayData) {
            foreach ($dayData['movies'] as $movieData) {
                $movie = Movie::updateOrCreate(
                    ['title' => $movieData['name']],
                );

                foreach ($movieData['showing'] as $showing) {
                    foreach ($showing['time'] as $time) {
                        $showtime = Showtime::updateOrCreate(
                            [
                                'movie_id' => $movie->id,
                                'show_date' => $showDate->toDateString(),
                                'time' => Carbon::parse($time)->toTimeString(),
                                'type' => $showing['type'],
                            ]
                        );
                        $this->addSeatsForShowtime($showtime);
                    }
                }
            }
            $showDate->addDay();
        }

        $this->info('Movies and showtimes synchronized successfully.');
    }

    protected function deletePastShowtimes() 
    {
        $today = Carbon::today();

        Showtime::where('show_date', '<', $today)->delete();

        $moviesToDelete = Movie::whereDoesntHave('showtimes')->get();

        foreach ($moviesToDelete as $movie) {
            $movie->delete();
        }

        $this->info('Past showtimes and unused movies have been deleted.');
    }

    /**
     * Menambahkan kursi ke dalam showtime
     *
     * @param Showtime $showtime
     */

    protected function addSeatsForShowtime(Showtime $showtime)
    {
        $existing = ShowingSeats::where('showtime_id', $showtime->id)->exists();

        if ($existing) {
            return;
        }

        $seats = Seat::all(); 
        $showingSeats = [];

        foreach ($seats as $seat) {
            $showingSeats[] = [
                'showtime_id' => $showtime->id,
                'seat_id' => $seat->id,
                'is_booked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        ShowingSeats::insert($showingSeats);
    }
}
