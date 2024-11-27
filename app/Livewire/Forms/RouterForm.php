<?php

namespace App\Livewire\Forms;

use App\Livewire\Contracts\FormCrudInterface;
use App\Models\Router;
use Livewire\Attributes\Validate;
use Livewire\Form;
use RouterOS\Client;

class RouterForm extends Form implements FormCrudInterface
{
    #[Validate('nullable|numeric')]
    public $id;

    #[Validate('required')]
    public $name = 'Mikrotik';

    #[Validate('required')]
    public $ipaddress = '192.168.10.1';

    #[Validate('required')]
    public $username = 'skripsi';

    #[Validate('required')]
    public $password = 'password';

    public $description = 'test';

    // Get the data
    public function getDetail($id)
    {
        $this->id = $id;
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
        try {

            $client = new Client([
                'host' => $this->ipaddress,
                'user' => $this->username,
                'pass' => $this->password,
                'port' => 8728,
            ]);
            //
            $response = $client->connect();

            if ($response) {
                Router::create([
                    'name' => $this->name,
                    'ip_address' => $this->ipaddress,
                    'username' => $this->username,
                    'password' => $this->password,
                    'description' => $this->description,
                ]);
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    // Update data
    public function update()
    {
        try {
            $router = Router::find($this->id);

            $client = new Client([
                'host' => $this->ipaddress,
                'user' => $this->username,
                'pass' => $this->password,
                'port' => 8728,
            ]);

            $response = $client->connect();

            if ($response) {
                $router->update([
                    'name' => $this->name,
                    'ip_address' => $this->ipaddress,
                    'username' => $this->username,
                    'password' => $this->password,
                    'description' => $this->description,
                ]);
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // Delete data
    public function delete($id)
    {
        Router::find($id)->delete();
    }
}
