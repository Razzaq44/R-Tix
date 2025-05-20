<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Showtime;

class RegenerateShowtimeSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'showtimes:regenerate-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate slugs for showtimes with missing or null slug';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $showtimes = Showtime::whereNull('slug')
            ->orWhere('slug', '')
            ->get();

        if ($showtimes->isEmpty()) {
            $this->info('No showtimes with null or empty slug found.');
            return 0;
        }

        foreach ($showtimes as $showtime) {
            $newSlug = $this->generateUniqueSlug($showtime);
            $showtime->slug = $newSlug;
            $showtime->save();

            $this->info("Updated slug for showtime ID {$showtime->id} to '{$newSlug}'");
        }

        $this->info('Slug regeneration completed.');
        return 0;
    }

    protected function generateUniqueSlug(Showtime $showtime)
    {
        $baseSlug = Str::slug($showtime->movie->title ?? 'jadwal');

        $slug = $baseSlug;
        $counter = 1;

        while (
            Showtime::where('slug', $slug)
                ->where('id', '!=', $showtime->id)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
}
