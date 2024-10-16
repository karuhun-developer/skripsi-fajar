<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public $name = "njjj";

    public function testaja()
    {
        $this->name = "wow";
    }
    public function render()
    {
        return view('livewire.dashboard');
    }
}
