<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Voucher;
use Carbon\Carbon;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Voucher::insert([
            [
                'code' => 'DISC10',
                'discount_amount' => 10,
                'discount_type' => 'percent', // atau 'fixed'
                'valid_until' => Carbon::now()->addDays(7),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MOVIE50K',
                'discount_amount' => 50000,
                'discount_type' => 'fixed',
                'valid_until' => Carbon::now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'HAPPYWEEKEND20',
                'discount_amount' => 20,
                'discount_type' => 'percent',
                'valid_until' => Carbon::now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
