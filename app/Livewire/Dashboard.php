<?php

namespace App\Livewire;

use App\Models\Subscription;
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

    public function render()
    {
        return view('livewire.dashboard');
    }
}
