<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckPaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-payment-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check payment status of subscriptions every day at midnight';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
