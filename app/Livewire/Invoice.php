<?php

namespace App\Livewire;

use App\Livewire\Forms\FormInvoice;
use App\Models\SubscriptionPayment;
use BaseComponent;
use Illuminate\Support\Facades\Auth;

class Invoice extends BaseComponent
{
    public FormInvoice $form;
    public $title = 'Invoice';
    public $searchBy = [
        [
            'name' => 'status',
            'field' => 'status',
        ],
    ];

    public $search = '';

    public $isUpdate = false;

    public $paginate = 10;

    public $orderBy = 'status';

    public $order = 'asc';

    public function render()
    {
        $model = SubscriptionPayment::whereHas('subscription', function ($query) {
            $query->where('user_id', Auth::id());
        });

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

        return view('livewire.invoice', compact('get'))->title($this->title);
    }
}
