<?php

namespace App\Livewire;

use App\Livewire\Forms\FormPppoe;
use App\Models\Plan;
use BaseComponent;

class Pppoe extends BaseComponent
{
    public $title = 'Management PPPoE';

    public FormPppoe $form;

    public $searchBy = [
        [
            'name' => 'Name',
            'field' => 'name',
        ],
    ];

    public $search = '';

    public $isUpdate = false;

    public $paginate = 10;

    public $orderBy = 'name';

    public $order = 'asc';

    public $routers = [];

    public function mount()
    {
        $this->routers = \App\Models\Router::all();
    }

    public function render()
    {
        $get = $this->getDataWithFilter(
            model: new Plan,
            searchBy: $this->searchBy,
            orderBy: $this->orderBy,
            order: $this->order,
            paginate: $this->paginate,
            s: $this->search
        );

        if ($this->search != null) {
            $this->resetPage();
        }

        return view('livewire.pppoe', compact('get'))->title($this->title);
    }
}
