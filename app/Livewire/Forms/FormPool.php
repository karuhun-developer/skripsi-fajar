<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use RouterOS\Query;
use App\Enums\Alert;
use App\Models\Pool;
use RouterOS\Client;
use Livewire\Attributes\Validate;
use App\Livewire\Contracts\FormCrudInterface;

class FormPool extends Form implements FormCrudInterface
{
    #[Validate('nullable|numeric')]
    public $id;

    #[Validate('required')]
    public $name;

    #[Validate('required')]
    public $localIp;

    #[Validate('required')]
    public $rangeIp;

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
    // app/Livewire/Forms/FormPool.php

    public function store()
    {
        try {
            $router = \App\Models\Router::find($this->routerId);
            $client = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => $router->port,
            ]);

            $query = new Query('/ip/pool/add');
            $query->equal('name', $this->name);
            $query->equal('local-address', $this->localIp);
            $query->equal('ranges', $this->rangeIp);

            $response = $client->query($query)->read();
            if (isset($response['after']['message'])) {
                throw new \Exception($response['after']['message']);
            }

            Pool::create([
                'name' => $this->name,
                'local_ip' => $this->localIp,
                'range_ip' => $this->rangeIp,
                'star' => $response['after']['ret'],
                'router_id' => $this->routerId,
            ]);


        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // Update data
    // app/Livewire/Forms/FormPool.php

    public function update()
    {
        try {
            $router = \App\Models\Router::find($this->routerId);
            Pool::find($this->id)->update([
                'name' => $this->name,
                'local_ip' => $this->localIp,
                'range_ip' => $this->rangeIp,
            ]);
            $client = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => $router->port,
            ]);

            $query = new Query('/ip/pool/set');
            $query->equal('.id', $this->star);
            $query->equal('name', $this->name);
            $query->equal('ranges', $this->rangeIp);
            $response = $client->query($query)->read();

            if (isset($response['after']['message'])) {
                throw new \Exception($response['after']['message']);
            }



        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // Delete data
    public function delete($id)
    {
        $pool = Pool::find($id)->delete();
        $router = $pool->router;

        // Delete from Mikrotik
        $client = new Client([
            'host' => $router->ip_address,
            'user' => $router->username,
            'pass' => $router->password,
            'port' => $router->port,
        ]);

        $query = new Query('/ip/pool/remove');
        $query->equal('.id', $pool->star);
        $response = $client->query($query)->read();

        if (isset($response['after']['message'])) throw new \Exception('Pool not found');

    }
}
