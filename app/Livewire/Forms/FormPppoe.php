<?php

namespace App\Livewire\Forms;

use App\Livewire\Contracts\FormCrudInterface;
use App\Models\Plan as PlanModel;
use Livewire\Attributes\Validate;
use Livewire\Form;

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
        PlanModel::create([
            'name' => $this->name,
            'bandwidth_id' => $this->bandwidthId,
            'pool_id' => $this->poolId,
            'price' => $this->price,
            'validity' => $this->validity,
            'validity_unit' => $this->validityUnit,
            'type' => $this->type,
        ]);
    }

    // Update data
    public function update() {}

    // Delete data
    public function delete($id) {}
}
