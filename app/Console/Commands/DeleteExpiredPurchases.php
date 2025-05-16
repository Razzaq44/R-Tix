<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Purchase;
use App\Models\ShowingSeats;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class DeleteExpiredPurchases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purchases:clear-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete pending purchases older than 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $purchases = Purchase::where('status', 'pending')
            ->where('created_at', '<', Carbon::now()->subMinutes(5))
            ->with('purchaseItems.showingSeat')
            ->get();
        DB::beginTransaction();

        try {
            foreach ($purchases as $purchase) {
                foreach ($purchase->purchaseItems as $item) {
                    $showingSeat = $item->showingSeat;
                    if ($showingSeat !== NULL) {
                        $showingSeat->update(['is_booked' => false]);
                    } else {
                        return;
                    }
                }
                $purchase->delete();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $this->info('Failed to clean up expired purchases '. $e);
        }
    
        $this->info('Expired pending purchases cleaned up.');
    }
}
