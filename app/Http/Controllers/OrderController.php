<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
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
    public function callback(Request $request)
    {


        $serverKey = getenv('MIDTRANS_SERVER_KEY');
        $hashedKey = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        $orderId = explode('-', $request->order_id,);
        $subscriptionPayment = SubscriptionPayment::where('order_id', $orderId[0])->first();

        $subscription = $subscriptionPayment->subscription;
        $user = $subscriptionPayment->subscription->user;
        $plan = $subscriptionPayment->subscription->plan;

        if ($hashedKey !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $transactionStatus = $request->transaction_status;


        switch ($transactionStatus){
            case 'capture':
                if ($request->payment_type == 'credit_card') {
                    if ($request->fraud_status == 'challenge') {
                        $subscriptionPayment->update(['status' => 'pending']);
                    } else {
                        $subscriptionPayment->update(['status' => 'success']);
                        $subscription->update(['status' => 'active', 'invoice_generated_at' => null]);
                        $this->updateSubscriptionExpiry($subscription);
                        $this->setPppoeProfile($user, $plan);

                    }
                }
                break;
            case 'settlement':
                $subscriptionPayment->update([
                    'status' => 'success']
                );
                $subscription->update([
                    'status' => 'active',
                    'invoice_generated_at' => null
                ]);

                $this->updateSubscriptionExpiry($subscription);

                $this->setPppoeProfile($user, $plan);

                break;
            case 'pending':
                $subscriptionPayment->update(['status' => 'pending']);
                break;
            case 'deny':
                $subscriptionPayment->update(['status' => 'failed']);
                break;
            case 'expire':
                $subscriptionPayment->update(['status' => 'expired']);
                break;
            case 'cancel':
                $subscriptionPayment->update(['status' => 'cancelled']);
                break;
            default:
                $subscriptionPayment->update(['status' => 'unknown']);
                break;
        }

        return response()->json(['message' => 'Callback received successfully']);

    }

    private function setPppoeProfile($user, $plan)
    {
        try {
            $client = new Client([
                'host' => $user->plan->pool->router->ip_address,
                'user' => $user->plan->pool->router->username,
                'pass' => $user->plan->pool->router->password,
                'port' => 8728,
            ]);
            $query = new Query('/ppp/secret/set');
            $query->equal('.id', $user->star);
            $query->equal('profile', $plan->name);

            $response = $client->query(endpoint: $query)->read();

        } catch (BadCredentialsException $e) {

        } catch (ConnectException $e) {

        } catch (ClientException $e) {

        } catch (ConfigException $e) {

        } catch (QueryException $e) {

        }
    }

    private function updateSubscriptionExpiry($subscription)
    {
        $subscription->update([
            'start_at' => $subscription->start_at->addDays(30),
            'expired_at' => $subscription->expired_at->addDays(60),
        ]);
    }
}
