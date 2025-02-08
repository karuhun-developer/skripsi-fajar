<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Exceptions\BadCredentialsException;
use RouterOS\Exceptions\ClientException;
use RouterOS\Exceptions\ConfigException;
use RouterOS\Exceptions\ConnectException;
use RouterOS\Exceptions\QueryException;
use RouterOS\Query;

class OrderController extends Controller
{
    protected $midtransService;
    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function callback(Request $request)
    {
        $orderId = explode('-', $request->order_id)[0];
        $subscriptionPayment = SubscriptionPayment::where('order_id', $orderId)->first();

        if (!$subscriptionPayment) {
            return response()->json(['message' => 'Subscription payment not found'], 404);
        }

        $subscription = $subscriptionPayment->subscription;
        $user = $subscription->user;
        $plan = $subscription->plan;

        if (!$this->midtransService->validateSignature($request)) {
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $this->handleTransactionStatus($request->transaction_status, $subscriptionPayment, $subscription, $user, $plan);

        return response()->json(['message' => 'Callback received successfully']);
    }

    protected function handleTransactionStatus($transactionStatus, $subscriptionPayment, $subscription, $user, $plan)
    {
        switch ($transactionStatus) {
            case 'capture':
                $this->handleCapture($subscriptionPayment, $subscription, $user, $plan);
                break;
            case 'settlement':
                $this->handleSettlement($subscriptionPayment, $subscription, $user, $plan);
                break;
            case 'pending':
                $subscriptionPayment->update(['status' => 'pending']);
                break;
            case 'deny':
                $subscriptionPayment->update(['status' => 'failed']);
                break;
            case 'expire':
                $subscriptionPayment->update(['status' => 'expired']);
                $subscription->update(['invoice_generated_at' => null]);
                break;
            case 'cancel':
                $subscriptionPayment->update(['status' => 'cancelled']);
                break;
            default:
                $subscriptionPayment->update(['status' => 'unknown']);
                break;
        }
    }
    protected function handleCapture($subscriptionPayment, $subscription, $user, $plan)
    {
        $subscriptionPayment->update(['status' => 'success']);
        $subscription->update(['status' => 'active', 'invoice_generated_at' => null]);
        $this->updateSubscriptionExpiry($subscription);
        $this->setPppoeProfile($user, $plan);
    }

    protected function handleSettlement($subscriptionPayment, $subscription, $user, $plan)
    {
        $subscriptionPayment->update(['status' => 'success']);
        $subscription->update(['status' => 'active', 'invoice_generated_at' => null]);
        $this->updateSubscriptionExpiry($subscription);
        $this->setPppoeProfile($user, $plan);
    }

    protected function updateSubscriptionExpiry($subscription)
    {
        $subscription->update([
            'start_at' => $subscription->start_at->addDays(30),
            'expired_at' => $subscription->expired_at->addDays(60),
        ]);
    }
    private function setPppoeProfile($user, $plan)
    {
        try {
            $client = new Client([
                'host' => $user->plan->pool->router->ip_address,
                'user' => $user->plan->pool->router->username,
                'pass' => $user->plan->pool->router->password,
                'port' => $user->plan->pool->router->port,
            ]);
            $query = new Query('/ppp/secret/set');
            $query->equal('.id', $user->star);
            $query->equal('profile', $plan->name);

            $response = $client->query(endpoint: $query)->read();

            if (isset($response['after']['message'])) {
                throw new \Exception($response['after']['message']);
            }

            $this->clearPppoeConnection($user->plan->pool->router, $user->pppoe_username);

        } catch (BadCredentialsException $e) {

        } catch (ConnectException $e) {

        } catch (ClientException $e) {

        } catch (ConfigException $e) {

        } catch (QueryException $e) {

        }
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

//                echo "Active connection for user {$pppoeUser} has been cleared.";
            } else {
//                echo "No active connection found for user {$pppoeUser}.";
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
