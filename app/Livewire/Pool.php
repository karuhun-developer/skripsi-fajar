<?php

namespace App\Livewire;

use App\Enums\Alert;
use App\Livewire\Forms\FormPool;
use App\Models\Pool as PoolModel;
use BaseComponent;

class Pool extends BaseComponent
{
    public $title = 'Management Pool';

    public FormPool $form;

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

    public function customSave() {
        try {
            $this->save();
        } catch(\Exception $e) {
            $this->dispatch('alert', type: Alert::error->value, message: $e->getMessage());
        }
    }
}
