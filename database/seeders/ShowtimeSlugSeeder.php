<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Showtime;
use Illuminate\Support\Str;

class ShowtimeSlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Showtime::with('movie')->get()->each(function ($showtime) {
            if ($showtime->movie) {
                $slug = Str::slug($showtime->movie->title . '-' . $showtime->id);
                $showtime->slug = $slug;
                $showtime->save();
            }
        });
    }
}
