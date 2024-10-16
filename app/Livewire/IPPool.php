<?php

namespace App\Livewire;

use App\Livewire\Forms\FormIPPool;
use BaseComponent;
use App\Models\Pool as PoolModel;
class IPPool extends BaseComponent
{
    public $title = "IP Pool";
    public FormIPPool $form;
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
        $model = PoolModel::where('name', 'like', '%' . $this->search . '%');

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
        return view('livewire.ippool', compact('get'))->title($this->title);
    }
}
