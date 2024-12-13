<?php

namespace App\Livewire;

use App\Models\SubscriptionPayment;
use BaseComponent;

class ListInvoices extends BaseComponent
{

    public $title = 'List Invoices';

    public $searchBy = [
        [
            'name' => 'Name',
            'field' => 'subscription.user.name',
        ],
    ];
    public $search = '';
    public $isUpdate = false;
    public $paginate = 10;
    public $orderBy = 'created_at';
    public $order = 'asc';
    public function render()
    {
        $get = $this->getDataWithFilter(
            model: new SubscriptionPayment,
            searchBy: $this->searchBy,
            orderBy: $this->orderBy,
            order: $this->order,
            paginate: $this->paginate,
            s: $this->search
        );

        if ($this->search != null) {
            $this->resetPage();
        }

        return view('livewire.list-invoices', compact('get'))->title($this->title);
    }
}
