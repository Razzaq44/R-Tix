<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Seat;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SeatSeeder::class);

        $this->call(updateSweetBoxSeats::class);
    }


}
