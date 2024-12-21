<?php

namespace App\Livewire\Forms;

use App\Helper\MikrotikClient;
use App\Livewire\Contracts\FormCrudInterface;
use App\Models\Router;
use App\Services\MikrotikService;
use Livewire\Attributes\Validate;
use Livewire\Form;
use RouterOS\Client;

class RouterForm extends Form implements FormCrudInterface
{
    #[Validate('nullable|numeric')]
    public $id;

    #[Validate('required')]
    public $name;

    #[Validate('required')]
    public $ipaddress;

    #[Validate('required|integer')]
    public $port;

    #[Validate('required')]
    public $username;

    #[Validate('required')]
    public $password;

    public $description;

    // Get the data
    public function getDetail($id)
    {
        $router = Router::find($id);
        $this->id = $id;
        $this->name = $router->name;
        $this->ipaddress = $router->ip_address;
        $this->port = $router->port;
        $this->username = $router->username;
        $this->password = $router->password;
        $this->description = $router->description;
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

        $response = MikrotikService::connect($this->username, $this->password, $this->ipaddress, $this->port);

        if ($response) {
            Router::create([
                'name' => $this->name,
                'ip_address' => $this->ipaddress,
                'port' => $this->port,
                'username' => $this->username,
                'password' => $this->password,
                'description' => $this->description,
            ]);
        }

    }

    // Update data
    public function update()
    {
        $router = Router::find($this->id);

        $response = MikrotikService::connect($this->username, $this->password, $this->ipaddress, $this->port);

        if ($response) {
            $router->update([
                'name' => $this->name,
                'ip_address' => $this->ipaddress,
                'port' => $this->port,
                'username' => $this->username,
                'password' => $this->password,
                'description' => $this->description,
            ]);
        }
    }

    // Delete data
    public function delete($id)
    {
        Router::find($id)->delete();
    }
}
