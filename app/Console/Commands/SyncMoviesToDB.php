<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\Showtime;
use App\Models\ShowingSeats;
use App\Models\Seat;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

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

        DB::beginTransaction();

        try {
            $showDate = Carbon::today();
            foreach ($showtimes as $dayData) {
                foreach ($dayData['movies'] as $movieData) {
                    $movie = Movie::updateOrCreate(
                        ['title' => $movieData['name']],
                    );
    
                    $showDateStr = $showDate->toDateString();
                    $existingShowtimes = Showtime::where('movie_id', $movie->id)
                        ->where('show_date', $showDateStr)
                        ->get();
    
                    foreach ($movieData['showing'] as $showing) {
                        foreach ($showing['time'] as $time) {
                            $timeStr = Carbon::parse($time)->toTimeString();
    
                            $exists = $existingShowtimes->firstWhere(function ($s) use ($timeStr, $showing) {
                                return $s->time === $timeStr && $s->type === $showing['type'];
                            });
    
                            if (!$exists) {
                                $slug = $this->generateUniqueSlug($movie->title);
    
                                $showtime = Showtime::create([
                                    'movie_id' => $movie->id,
                                    'show_date' => $showDateStr,
                                    'time' => $timeStr,
                                    'type' => $showing['type'],
                                    'slug' => $slug,
                                ]);
    
                                $this->addSeatsForShowtime($showtime);
                            }
                        }
                    }
                }
                $showDate->addDay();
            }

            $this->addUrlImageToDB();

            DB::commit();

            $this->info('Movies and showtimes synchronized successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            $this->info('Failed to sync movies '. $e);
        }
    }

    protected function deletePastShowtimes() 
    {
        $today = Carbon::today();

        DB::beginTransaction();
        
        try {
            Showtime::where('show_date', '<', $today)->delete();
    
            $moviesToDelete = Movie::whereDoesntHave('showtimes')->get();
    
            foreach ($moviesToDelete as $movie) {
                $movie->delete();
            }

            DB::commit();

            $this->info('Past showtimes and unused movies have been deleted.');
        } catch (Exception $e) {
            DB::rollBack();
            $this->info('Failed to delete past showtimes '. $e);
        }
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

    /**
     * Generate unique slug for showtime
     *
     * @param string $title
     * @return string
     */
    protected function generateUniqueSlug($title)
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $count = 1;

        while (Showtime::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Generate unique slug for showtime
     *
     * @param 
     */

    protected function addUrlImageToDB()
    {
        $cacheFilePath = storage_path('app/movies.json');

        if (!file_exists($cacheFilePath)) {
            $this->error('Cache file not found.');
            return;
        }

        $cachedMovies = json_decode(file_get_contents($cacheFilePath), true);
        if (!$cachedMovies || !is_array($cachedMovies)) {
            $this->error('Invalid movie data in cache.');
            return;
        }

        $movies = Movie::all();

        foreach ($movies as $movie) {
            foreach ($cachedMovies as $cachedMovie) {
                if (strtolower(trim($movie->title)) === strtolower(trim($cachedMovie['name'] ?? ''))) {
                    $urlImage = $cachedMovie['image'] ?? null;
                    if ($urlImage && $movie->url_image !== $urlImage) {
                        $movie->url_image = $urlImage;
                        $movie->save();
                        $this->info("Updated image for: {$movie->title}");
                    }
                    break;
                }
            }
        }

        $this->info("Image URLs updated in database.");
    }
}
