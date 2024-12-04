<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use RouterOS\Client;
use RouterOS\Query;

class IsolateUnpaidSubscriptionss extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:isolate-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Isolate internet for subscriptions that expired 7 days ago and are still unpaid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the date 7 days ago
        $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString();

        $subscriptions = Subscription::whereDate('expired_at', $sevenDaysAgo)
            ->where('status', 'throttle')
            ->get();

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            $plan = $subscription->plan;
            $this->isolateInternet($user, $plan);
        }
    }

    private function isolateInternet($user, $plan)
    {
        $router = $user->plan->pool->router;
        $client = new Client([
            'host' => $router->ip_address,
            'user' => $router->username,
            'pass' => $router->password,
            'port' => $router->port,
        ]);

        $query = new Query('/ppp/secret/set');
        $query->equal('.id', $user->star);
        $query->equal('profile', "pppoe-expired");
        $response = $client->query($query)->read();

        if (isset($response['after']['message'])) {
            throw new \Exception($response['after']['message']);
        }

        $this->clearPppoeConnection($router, $user->pppoe_username);

        $subscription = Subscription::where('user_id', $user->id)->where('status', 'throttle')->update(['status' => 'isolated']);
    }

    function clearPppoeConnection($router, $pppoeUser)
    {
        try {

            $client = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => (int) $router->port,
            ]);


            $query = new Query('/ppp/active/print');
            $query->where('name', $pppoeUser);
            $response = $client->query($query)->read();

            if (!empty($response)) {
                // Remove active connection
                $activeId = $response[0]['.id'];
                $removeQuery = new Query('/ppp/active/remove');
                $removeQuery->equal('.id', $activeId);
                $client->query($removeQuery)->read();

                echo "Active connection for user {$pppoeUser} has been cleared.";
            } else {
                echo "No active connection found for user {$pppoeUser}.";
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
