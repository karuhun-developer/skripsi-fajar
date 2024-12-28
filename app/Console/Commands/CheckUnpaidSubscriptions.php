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
            $plan = $subscription->plan;
            $this->reduceBandwidth($user, $plan);
        }

    }

    private function reduceBandwidth($user, $plan)
    {
        $router = $user->plan->pool->router;
        $mikrotikService = new MikrotikService($router->id);
        $response = $mikrotikService->executeQuery('/ppp/secret/set', [
            '.id' => $user->star,
            'profile' => $plan->name . '_DOWNGRADE_50',
        ]);

        if (!isset($response['after']['message'])){
            $this->info("Bandwidth for user {$user->pppoe_username} has been reduced by 50%.");
            $subscriptions = Subscription::whereDate('expired_at', DB::raw('CURDATE()'))
                ->update(['status' => 'throttle']);
        }else{
            $this->error($response['after']['message']);
        }
    }
}
