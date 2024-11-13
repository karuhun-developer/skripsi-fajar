<?php

namespace App\Console\Commands;

use App\Models\Subscription;
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
        $subscriptions = Subscription::whereDate('expired_at', DB::raw('CURDATE()'))->get();
        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            $plan = $subscription->plan;
            $this->generatePaymentLink($user, $plan);
        }

        $this->info('Invoices generated successfully.');
    }

    public function generatePaymentLink($user, $plan)
    {
        $params = [
            'transaction_details' => [
                'order_id' => uniqid(),
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

        $response = Http::withBasicAuth("SB-Mid-server-o8MFp0Ybs8U6k0BuDddYsqmb", '')->post(
            'https://api.sandbox.midtrans.com/v1/payment-links',
            $params
        );

        // Handle the response if needed
        if ($response->successful()) {
            // Log or perform any action if needed
        } else {
            // Handle the error
            $this->error('Failed to generate payment link for user: ' . $user->email);
        }
    }
}
