<?php

namespace App\Livewire\Forms;

use App\Livewire\Contracts\FormCrudInterface;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RouterForm extends Form implements FormCrudInterface
{
    #[Validate('nullable|numeric')]
    public $id;

    #[Validate('required')]
    public $name;

    #[Validate('required')]
    public $ipaddress;

    #[Validate('required')]
    public $username;

    #[Validate('required')]
    public $password;

    public $description;

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

    }

    // Update data
    public function update() {

    }

    // Delete data
    public function delete($id) {

    }
}
