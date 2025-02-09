<?php

namespace App\Livewire;

use App\Helper\MikrotikClient;
use App\Models\Subscription;
use App\Models\User;
use App\Services\MikrotikService;
use Carbon\Carbon;
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

    public $userCounts = [];

    public function mount()
    {
        $this->countUsersByStatus();
    }

    public function countUsersByStatus()
    {
        $this->userCounts = [
            'active' => Subscription::where('status', 'active')->count(),
            'inactive' => Subscription::where('status', 'inactive')->count(),
            'throttle' => Subscription::where('status', 'throttle')->count(),
            'isolated' => Subscription::where('status', 'isolated')->count(),
        ];
    }

    public function render()
    {

        if(auth()->user()->hasRole(['admin', 'noc', 'finance'])) {
            return view('livewire.dashboard', [
                'userCounts' => $this->userCounts,
            ]);
        } else {

            $client = new MikrotikClient([
                'host' => auth()->user()->plan->pool->router->ip_address,
                'user' => auth()->user()->plan->pool->router->username,
                'pass' => auth()->user()->plan->pool->router->password,
                'port' => (int) auth()->user()->plan->pool->router->port,
            ]);


            $query = new Query('/interface/print');
            $query->where('name', "<pppoe-" . auth()->user()->pppoe_username . ">");
            $response = $client->query($query)->read();

            return view('livewire.dashboard-user', [
                'response' => $response,
            ]);
        }
    }
}
