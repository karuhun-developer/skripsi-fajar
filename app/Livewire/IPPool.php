<?php

namespace App\Livewire;

use BaseComponent;

class IPPool extends BaseComponent
{
    public $title = "IP Pool";
    public function render()
    {
        return view('livewire.ippool');
    }
}
