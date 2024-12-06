<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
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
        $subscriptions = Subscription::whereDate('start_at', '<=', DB::raw('CURDATE()'))
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
        $response = PaymentLinkGenerator::generate($user, $plan, $subscription);

        $paymentLink = $response['payment_url'];

        if ($response['successful']) {

            SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'bill_amount' => $plan->price,
                'order_id' => uniqid(),
                'payment_link' => $paymentLink,
                'status' => 'pending',
                'created_at' => now(),
            ]);

            $subscription->update(['invoice_generated_at' => now()]);

        } else {
            $this->error('Failed to generate payment link for user: ' . $user->email);
        }
    }
}
