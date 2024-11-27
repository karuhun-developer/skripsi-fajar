<?php

namespace App\Livewire;

use App\Enums\Alert;
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

        $get = $this->getDataWithFilter(
            model: new RouterModel,
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

    public function customSave() {
        try {
            $this->save();
        } catch(\Exception $e) {
            $this->dispatch('alert', type: Alert::error->value, message: $e->getMessage());
        }
    }
}
