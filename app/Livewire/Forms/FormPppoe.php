<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use RouterOS\Query;
use App\Models\Pool;
use RouterOS\Client;
use App\Models\Bandwidth;
use App\Models\Plan as PlanModel;
use Livewire\Attributes\Validate;
use App\Livewire\Contracts\FormCrudInterface;

class FormPppoe extends Form implements FormCrudInterface
{
    #[Validate('nullable|numeric')]
    public $id;

    #[Validate('required')]
    public $name;

    // #[Validate('required')]
    public $bandwitdh;

    #[Validate('required')]
    public $price;

    // #[Validate('required')]
    public $poolId;

    #[Validate('required')]
    public $validity;

    // #[Validate('required')]
    public $validityUnit;

    // #[Validate('required')]
    public $bandwidthId;
    public $type = 'PPPOE';

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
        $dataBandwidth = Bandwidth::where('id', $this->bandwidthId)->first();
        $dataPool = Pool::where('id', $this->poolId)->first();
        $rateLimit = $dataBandwidth->rate_download . 'M/' . $dataBandwidth->rate_upload . 'M ' . $dataBandwidth->burst;

        $client = new Client([
            'host' => $dataPool->router->ip_address,
            'user' => $dataPool->router->username,
            'pass' => $dataPool->router->password,
        ]);

        $query = new Query('/ppp/profile/add');
        $query->equal('name', $this->name);
        $query->equal('local-address', $dataPool->local_ip);
        $query->equal('remote-address', $dataPool->name);
        $query->equal('rate-limit', $rateLimit);
        $response = $client->query($query)->read();

        if ($response['after']['ret'] != null){
            PlanModel::create([
                'name' => $this->name,
                'bandwidth_id' => $this->bandwidthId,
                'pool_id' => $this->poolId,
                'price' => $this->price,
                'validity' => $this->validity,
                'validity_unit' => $this->validityUnit,
                'type' => $this->type,
                'star' => $response['after']['ret'],
        ]);
        }
    }

    // Update data
    public function update() {}

    // Delete data
    public function delete($id) {}
}
