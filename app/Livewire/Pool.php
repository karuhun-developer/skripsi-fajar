<?php

namespace App\Livewire;

use App\Livewire\Forms\FormPool;
use App\Models\Pool as PoolModel;
use BaseComponent;

class Pool extends BaseComponent
{
    public $title = "Management Pool";
    public FormPool $form;
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
    public $routers = [];
    public function mount() {
        $this->routers = \App\Models\Router::all();
    }

    public function render()
    {

        $get = $this->getDataWithFilter(
            model: new PoolModel,
            searchBy: $this->searchBy,
            orderBy: $this->orderBy,
            order: $this->order,
            paginate: $this->paginate,
            s: $this->search
        );

        if ($this->search != null) {
            $this->resetPage();
        }
        return view('livewire.pool', compact('get'))->title($this->title);
    }
}
