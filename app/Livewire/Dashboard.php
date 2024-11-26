<?php

namespace App\Livewire;

use App\Models\Subscription;
use App\Models\User;
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
            'pending' => Subscription::where('status', 'pending')->count(),
        ];
    }

    public function render()
    {

        if(auth()->user()->hasRole(['admin'])) {
            return view('livewire.dashboard', [
                'userCounts' => $this->userCounts,
            ]);
        } else {
            return view('livewire.dashboard-user');
        }
    }
}
