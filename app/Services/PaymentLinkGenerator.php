<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class PaymentLinkGenerator
{
    public static function generate($user, $plan, $subscription)
    {
        $orderId = uniqid();
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $plan->price,
                'payment_link_id' => 'akses-net-' . $user->id . '-' . $plan->id . '-' . uniqid(),
            ],
            'customer_required' => true,
            'usage_limit' => 1,
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s') . ' +0700',
                'duration' => 20,
                'unit' => 'days',
            ],
            'item_details' => [
                [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => $plan->price,
                    'quantity' => 1,
                    'brand' => 'ID Fiber',
                    'category' => 'Internet',
                    'merchant_name' => 'PT. Media Akses Data',
                ],
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'last_name' => $user->name,
                'email' => $user->email,
                'phone' => '+62181000000000',
            ],
        ];

        $midtransServerKey = env('MIDTRANS_SERVER_KEY');
        $midtransPaymentLink = env('MIDTRANS_PAYMENT_LINK');

        try {
            $response = Http::withBasicAuth($midtransServerKey, '')->post(
                $midtransPaymentLink,
                $params
            );
        } catch (ConnectionException $e) {
            // Handle connection exception
        }

        return [
            'successful' => $response->successful(),
            'payment_url' => $response->json()['payment_url'] ?? null,
        ];
    }
}
