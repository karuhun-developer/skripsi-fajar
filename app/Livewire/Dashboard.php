<?php

namespace App\Livewire;

use App\Models\Subscription;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Midtrans\Snap;
use RouterOS\Client;
use RouterOS\Query;

class Dashboard extends Component
{
    public $name = "njjj";
    public $datas = [];

    public function testaja()
    {
        $this->name = "wow";
    }

    public function render()
    {
        return view('livewire.dashboard');
    }

    public function getPool()
    {

        $subscriptions = Subscription::whereDate('expired_at', DB::raw('CURDATE()'))->get();
        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            $plan = $subscription->plan;
            // Lakukan sesuatu dengan data user
            // Contoh: echo $user->name;
            $this->generatePaymentLink($user, $plan);
        }


//        $client = new Client([
//            'host' => '192.168.10.1',
//            'user' => 'skripsi',
//            'pass' => 'password',
//        ]);
//
//        try {
//            foreach ($this->datas as $subscription) {
//                $user = $subscription->user;
//                $query = new Query('/ppp/secret/set');
//                $query->equal('.id', $user->star);
//                $query->equal('profile', 'pppoe-expired');
//
//                $response = $client->query($query)->read();
//                // Handle the response if needed
//            }
//        } catch (Exception $e) {
//            echo 'Error: ' . $e->getMessage();
//        }
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
                'start_time' => '2024-12-13 18:00 +0700',
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

     dd($response);
    }
}
