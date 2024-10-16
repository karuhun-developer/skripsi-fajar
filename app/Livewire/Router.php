<?php

namespace App\Livewire;

use App\Livewire\Forms\RouterForm;
use BaseComponent;
use App\Models\Router as RouterModel;

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
        $model = RouterModel::where('name', 'like', '%' . $this->search . '%');

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
        return view('livewire.router', compact('get'))->title($this->title);
    }
}
