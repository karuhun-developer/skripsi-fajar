<?php

namespace App\Livewire;

use App\Livewire\Forms\FormBandwidth;
use App\Models\Bandwidth as ModelsBandwidth;
use BaseComponent;

class Bandwidth extends BaseComponent
{
    public $title = 'Management Bandwidth';

    public FormBandwidth $form;

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
    public function render()
    {
        $get = $this->getDataWithFilter(
            model: new ModelsBandwidth,
            searchBy: $this->searchBy,
            orderBy: $this->orderBy,
            order: $this->order,
            paginate: $this->paginate,
            s: $this->search
        );

        if ($this->search != null) {
            $this->resetPage();
        }
        return view('livewire.bandwidth', compact('get'))->title($this->title);
    }
}
