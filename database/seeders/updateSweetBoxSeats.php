<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Seat;

class updateSweetBoxSeats extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Seat::where('seat_number', 'LIKE', 'A%')
            ->update(['type' => 'sweetbox']);
    }
}
