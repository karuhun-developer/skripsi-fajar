<?php

namespace App\Livewire\Forms;

use App\Livewire\Contracts\FormCrudInterface;
use App\Models\Pool;
use Livewire\Attributes\Validate;
use Livewire\Form;
use RouterOS\Client;
use RouterOS\Query;

class FormPool extends Form implements FormCrudInterface
{
    #[Validate('nullable|numeric')]
    public $id;

    #[Validate('required')]
    public $name = 'Mikrotik A';

    #[Validate('required')]
    public $localIp = '192.168.90.1';

    #[Validate('required')]
    public $rangeIp = '192.168.90.2-192.168.90.80';

    #[Validate('required')]
    public $star = '*70';

    #[Validate('required')]
    public $routerId;

    // Get the data
    public function getDetail($id)
    {
        $data = Pool::find($id);
        $this->id = $id;
        $this->name = $data->name;
        $this->localIp = $data->local_ip;
        $this->rangeIp = $data->range_ip;
        $this->star = $data->star;
        $this->routerId = $data->router_id;
    }

    // Save the data
    public function save()
    {
        $this->validate();

        if ($this->id) {
            $this->update();
        } else {
            $this->store();
        }

        $this->reset();
    }

    // Store data
    public function store()
    {
        $router = \App\Models\Router::find($this->routerId);
        $client = new Client([
            'host' => $router->ip_address,
            'user' => $router->username,
            'pass' => $router->password,
            'port' => 8728,
        ]);

        $query = new Query('/ip/pool/add');
        $query->equal('name', $this->name);
        $query->equal('ranges', $this->rangeIp);

        $response = $client->query($query)->read();
        if ($response) {
            Pool::create([
                'name' => $this->name,
                'local_ip' => $this->localIp,
                'range_ip' => $this->rangeIp,
                'star' => $response['after']['ret'],
                'router_id' => $this->routerId,
            ]);
        }

    }

    // Update data
    public function update()
    {

        $router = \App\Models\Router::find($this->routerId);
        $client = new Client([
            'host' => $router->ip_address,
            'user' => $router->username,
            'pass' => $router->password,
            'port' => 8728,
        ]);

        $query = new Query('/ip/pool/set');
        $query->equal('.id', $this->star);
        $query->equal('name', $this->name);
        $query->equal('ranges', $this->rangeIp);
        $response = $client->query($query)->read();

        if ($response == null) {
            Pool::find($this->id)->update([
                'name' => $this->name,
                'local_ip' => $this->localIp,
                'range_ip' => $this->rangeIp,
            ]);
        }
    }

    // Delete data
    public function delete($id)
    {
        $pool = Pool::find($id);
        $router = $pool->router;
        //delete from mikrotik
        try {
            $client = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => 8728,
            ]);

            $query = new Query('/ip/pool/remove');
            $query->equal('.id', $pool->star);
            $response = $client->query($query)->read();

            //delete from database
            if ($response == null) {
                $pool->delete();
            }

        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }
}
