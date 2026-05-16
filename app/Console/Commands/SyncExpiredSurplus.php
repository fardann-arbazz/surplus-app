<?php

namespace App\Console\Commands;

use App\Models\SurplusProduct;
use App\Events\SurplusStatusUpdated;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncExpiredSurplus extends Command
{
    protected $signature   = 'surplus:sync-expired';
    protected $description = 'Update status surplus yang sudah expired';

    public function handle(): void
    {
        // Ambil semua yang harusnya expired tapi masih active
        $expiredSurplus = SurplusProduct::query()
            ->where('status', 'active')
            ->where('expired_at', '<=', Carbon::now())
            ->get();

        foreach ($expiredSurplus as $surplus) {
            $surplus->update(['status' => 'expired']);

            // Broadcast ke seller (channel per store)
            broadcast(new SurplusStatusUpdated($surplus))->toOthers();

            $this->info("Expired: surplus #{$surplus->id}");
        }

        $this->info("Total expired: {$expiredSurplus->count()}");
    }
}
