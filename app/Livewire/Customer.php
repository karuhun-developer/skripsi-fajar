<?php

namespace App\Livewire;

use App\Livewire\Forms\FormCustomer;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User as UserModel;
use BaseComponent;

class Customer extends BaseComponent
{
    public FormCustomer $form;
    public $title = 'Management Customer';

    public $searchBy = [
        [
            'name' => 'Name',
            'field' => "name",
        ],
        [
            'name' => 'Email',
            'field' => 'email',
        ],
        [
            'name' => 'Role',
            'field' => 'roles.name',
        ],
    ],
        $search = '',
        $isUpdate = false,
        $paginate = 10,
        $orderBy = 'name',
        $order = 'asc';

    public $plans = [];
    public function mount()
    {
        // Add modal for update password
        $this->addModal('updatePasswordModal');

        // Get roles
        $this->plans = Plan::all();
    }

    public function render()
    {
        //get only user with role customer
        $model = UserModel::whereHas('roles', function ($query) {
            $query->where('name', 'customer');
        })->with(['roles', 'plan.subscriptions',]);
//        $model = UserModel::with(['roles', 'plan.subscriptions',]);


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

        return view('livewire.customer', compact('get'))->title($this->title);
    }
}
