<?php

namespace App\Livewire;

use BaseComponent;

class Router extends BaseComponent
{
    public $title = "Management Router";
    public function render()
    {
        return view('livewire.router');
    }
}
