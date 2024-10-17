<?php

namespace App\Livewire;

use App\Livewire\Forms\FormPool;
use BaseComponent;

class Pool extends BaseComponent
{
    public $title = "Management Router";
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
        $model = \App\Models\Pool::where('name', 'like', '%' . $this->search . '%');

        $get = $this->getDataWithFilter(
            model: $model,
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
