<?php

namespace App\Livewire\Forms;

use App\Livewire\Contracts\FormCrudInterface;
use App\Models\Pool;
use Livewire\Attributes\Validate;
use Livewire\Form;

class FormPool extends Form implements FormCrudInterface
{
    #[Validate('nullable|numeric')]
    public $id;

    // Get the data
    public function getDetail($id) {
        $this->id = $id;
    }

    // Save the data
    public function save() {
        $this->validate();

        if ($this->id) {
            $this->update();
        } else {
            $this->store();
        }

        $this->reset();
    }

    // Store data
    public function store() {
        Pool::create([
            'name' => 'Mikrotik A',
            'local_ip' => '192.168.90.1',
            'range_ip' => '192.168.90.2-192.168.90.80',
            'star' => '*70',
            'router_id' => 1,
        ]);
    }

    // Update data
    public function update() {

    }

    // Delete data
    public function delete($id) {

    }
}
