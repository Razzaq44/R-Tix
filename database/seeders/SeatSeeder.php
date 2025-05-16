<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Seat;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range('A', 'J') as $row) {
            foreach (range(1, 30) as $number) {
                Seat::firstOrCreate(
                    ['seat_number' => $row . $number],
                    ['type' => $row === 'A' ? 'sweetbox' : 'regular']
                );
            }
        }
    }
}
