<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GenerateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices for customers whose subscriptions are due';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subscriptions = Subscription::whereDate('start_at', DB::raw('CURDATE()'))
            ->whereNull('invoice_generated_at')
            ->get();
        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            $plan = $subscription->plan;
            $this->generatePaymentLink($user, $plan, $subscription);
        }

        $this->info('Invoices generated successfully.');
    }

    public function generatePaymentLink($user, $plan, $subscription)
    {
        $orderId = uniqid();
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $plan->price,
                'payment_link_id' => 'akses-net-' . $user->id . '-' . $plan->id . '-' . uniqid(),
            ],
            'customer_required' => true,
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

        $response = Http::withBasicAuth(getenv("MIDTRANS_SERVER_KEY"), '')->post(
            getenv("MIDTRANS_PAYMENT_LINK"),
            $params
        );

        $paymentLink = $response->json()['payment_url'];
        // Handle the response if needed
        if ($response->successful()) {
            // Log or perform any action if needed
            SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'bill_amount' => $plan->price,
                'order_id' => $orderId,
                'payment_link' => $paymentLink,
                'status' => 'pending',
                'created_at' => now(),
            ]);

            $subscription->update(['invoice_generated_at' => now()]);

        } else {
            // Handle the error
            $this->error('Failed to generate payment link for user: ' . $user->email);
        }
    }
}
