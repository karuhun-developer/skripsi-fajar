<?php

namespace App\Livewire;

use Exception;
use Livewire\Component;
use RouterOS\Client;
use RouterOS\Query;

class Dashboard extends Component
{
    public $name = "njjj";

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
        $client = new Client([
            'host' => '192.168.10.1',
            'user' => 'phpnuxbill',
            'pass' => 'password',
        ]);

        try {
//            $query = new Query('/ip/pool/add');
//            $query->equal('name', 'babi anjing');
//            $query->equal('ranges', '192.168.100.10-192.168.100.100');
//
//            $response = $client->query($query)->read();
//            dd($response);
//
            $query = new Query('/ip/pool/remove');
            $query->equal('.id', '*14');

            $response = $client->query($query)->read();
            dd($response);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
