<?php

namespace App\Livewire\Forms;

use App\Models\Router;
use App\Services\MikrotikService;
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
    public $description;
    protected $mikrotikService;

    public function getDetail($id)
    {
        $data = Pool::find($id);
        $this->id = $id;
        $this->name = $data->name;
        $this->localIp = $data->local_ip;
        $this->rangeIp = $data->range_ip;
        $this->star = $data->star;
        $this->routerId = $data->router_id;
        $this->description = $data->description;
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
        $router = Router::find($this->routerId);
        $mikrotikService = new MikrotikService(
            $router->username,
            $router->password,
            $router->ip_address,
            $router->port
        );
        $response = $mikrotikService->addPool($this->name, $this->description, $this->rangeIp);

        if (isset($response['after']['message'])) {
            throw new \Exception($response['after']['message']);
        }

        Pool::create([
            'name' => $this->name,
            'local_ip' => $this->localIp,
            'range_ip' => $this->rangeIp,
            'description' => $this->description,
            'star' => $response['after']['ret'],
            'router_id' => $this->routerId,
        ]);
    }

    public function update()
    {
        $pool = Pool::find($this->id);

        $pool->update([
            'name' => $this->name,
            'local_ip' => $this->localIp,
            'range_ip' => $this->rangeIp,
            'description' => $this->description,
        ]);

        $mikrotikService = new MikrotikService(
            $pool->router->username,
            $pool->router->password,
            $pool->router->ip_address,
            $pool->router->port
        );
        $response = $mikrotikService->updatePool(
            $pool->star,
            $this->name,
            $this->rangeIp,
            $this->description
        );

        if (isset($response['after']['message'])) {
            throw new \Exception($response['after']['message']);
        }
    }

    // Delete data
    public function delete($id)
    {
        $pool = Pool::find($id);
        $router = $pool->router;

        $mikrotikService = new MikrotikService(
            $router->username,
            $router->password,
            $router->ip_address,
            $router->port
        );
        $response = $mikrotikService->deletePool($pool->star);

        if (isset($response['after']['message'])) throw new \Exception('Pool not found');
        $pool->delete();

    }
}
