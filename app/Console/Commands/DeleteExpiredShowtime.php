<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ShowingSeats;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class DeleteExpiredShowtime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:expired-showtime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $showtime = Showtime::where('time', '<=', Carbon::now()->subMinutes(30)->format('H:i:s'))
            ->where('show_date', '<=', Carbon::now()->format('Y-m-d'))
            ->with('showingSeats')->get();
            DB::beginTransaction();

        try {
            foreach ($showtime as $show) {
                foreach ($show->showingSeats as $showingSeat) {
                    $showingSeat->delete();
                }
                $show->delete();
            }

            DB::commit();

            $this->info("Successesful delete expired show.");
        } catch (Exception $e) {
            DB::rollBack();
            $this->info($e);
        }
    }
}
