<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Services\MidtransService;
use App\Services\PaymentLinkGenerator;
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
//        $subscriptions = Subscription::whereDate('start_at', '<=', DB::raw('CURDATE()'))
//            ->whereNull('invoice_generated_at')
//            ->get();
        $subscriptions = Subscription::due()->get();
        foreach ($subscriptions as $subscription) {
            $this->generatePaymentLink($subscription);
        }
    }

    public function generatePaymentLink($subscription)
    {
        $orderId = uniqid();
        $response = MidtransService::createPaymentLink($orderId, $subscription->user, $subscription->plan);

        if ($response['successful']) {
            $this->info('Payment link generated for user: ' . $subscription->user->email);

            SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'bill_amount' => $subscription->plan->price,
                'order_id' => $orderId,
                'payment_link' => $response['payment_url'],
                'status' => 'pending',
                'created_at' => now(),
            ]);

            $subscription->update(['invoice_generated_at' => now()]);

        } else {
            $this->error('Failed to generate payment link for user: ' . $subscription->user->email);
        }
    }
}
