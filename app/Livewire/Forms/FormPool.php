<?php

namespace App\Livewire\Forms;

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

    protected function getMikrotikService()
    {
        if (!$this->mikrotikService || $this->mikrotikService->getRouterId() !== $this->routerId) {
            $this->mikrotikService = new MikrotikService($this->routerId);
        }

        return $this->mikrotikService;
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
        $mikrotikService = $this->getMikrotikService();
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
            Pool::find($this->id)->update([
                'name' => $this->name,
                'local_ip' => $this->localIp,
                'range_ip' => $this->rangeIp,
                'description' => $this->description,
            ]);

            $mikrotikService = $this->getMikrotikService();
            $response = $mikrotikService->updatePool(
                $this->star,
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

        $mikrotikService = new MikrotikService($router->id);
        $response = $mikrotikService->deletePool($pool->star);

        if (isset($response['after']['message'])) throw new \Exception('Pool not found');
        $pool->delete();

    }
}
