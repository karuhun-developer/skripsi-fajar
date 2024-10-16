<?php

namespace App\Livewire;

use App\Livewire\Forms\RouterForm;
use BaseComponent;

class Router extends BaseComponent
{
    public $title = "Management Router";
    public RouterForm $form;
    public $searchBy = [
        [
            'name' => 'Name',
            'field' => 'name',
        ],
    ],
        $search = '',
        $isUpdate = false,
        $paginate = 10,
        $orderBy = 'name',
        $order = 'asc';

    public function render()
    {
        return view('livewire.router');
    }
}
