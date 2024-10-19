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

    // #[Validate('required')]
    public $name = 'PPPoE A';

    // #[Validate('required')]
    public $bandwitdh = '';

    // #[Validate('required')]
    public $price = '';

    // #[Validate('required')]
    public $pool;

    // #[Validate('required')]
    public $validity;

    // #[Validate('required')]
    public $validityUnit;

    public $pools = [];

    public $routers = [];

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
            'bandwidth_id' => 1,
            'pool_id' => 4,
            'price' => 80000,
            'validity' => 5,
            'validity_unit' => 'day',
            'type' => 'PPPOE',
        ]);
    }

    // Update data
    public function update() {}

    // Delete data
    public function delete($id) {}
}
