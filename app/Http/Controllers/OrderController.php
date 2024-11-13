<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function callback(Request $request)
    {


        $serverKey = getenv('MIDTRANS_SERVER_KEY');
        $hashedKey = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        $subscriptionPayment = SubscriptionPayment::where('snap_token', $request->order_id)->first();

        if ($hashedKey !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $transactionStatus = $request->transaction_status;
        $orderId = $request->order_id;

        switch ($transactionStatus){
            case 'capture':
                if ($request->payment_type == 'credit_card') {
                    if ($request->fraud_status == 'challenge') {
                        $subscriptionPayment->update(['status' => 'pending']);
                    } else {
                        $subscriptionPayment->update(['status' => 'success']);
                    }
                }
                break;
            case 'settlement':
                $subscriptionPayment->update(['status' => 'success']);
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
}
