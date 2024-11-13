<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class CheckUnpaidSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check unpaid subscriptions for over a month and reduce bandwidth by 50%';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oneMonthAgo = now()->subMonth();

        $subscriptions = Subscription::where('end_at', '<', $oneMonthAgo)
            ->where('status', 'unpaid')
            ->get();
    }
}
