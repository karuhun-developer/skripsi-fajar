<?php

namespace App\Console\Commands;

use App\Services\MikrotikService;
use RouterOS\Query;
use RouterOS\Client;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        $subscriptions = Subscription::whereDate('expired_at', DB::raw('CURDATE()'))
            ->where('status', 'active')
            ->get();

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            $this->reduceBandwidth($user, $subscription);
        }

    }

    private function reduceBandwidth($user, $subscription)
    {
        $router = $user->plan->pool->router;
        $plan = $user->plan;
        $mikrotikService = new MikrotikService($router);
        $response = $mikrotikService->executeQuery('/ppp/secret/set', [
            '.id' => $user->star,
            'profile' => $plan->name . '_DOWNGRADE_50',
        ]);

        if (!isset($response['after']['message'])){
            $subscription->update(['status' => 'throttle']);
            $this->info("Bandwidth for user {$user->pppoe_username} has been reduced by 50%.");
        }else{
            $this->error($response['after']['message']);
        }
    }
}
