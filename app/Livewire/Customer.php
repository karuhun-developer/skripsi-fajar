<?php

namespace App\Livewire;

use App\Enums\Alert;
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
    ],
        $search = '',
        $isUpdate = false,
        $paginate = 10,
        $orderBy = 'name',
        $order = 'asc';

    public $plans = [];
    public $roles = [];
    public function mount()
    {
        // Add modal for update password
        $this->addModal('updatePasswordModal');

        // Get roles
        $this->roles = Role::all();
        $this->plans = Plan::all();
    }

    public function render()
    {
        //get only user with role customer
        $model = UserModel::whereHas('roles', function ($query) {
            $query->where('name', 'customer');
        })
        ->with(['roles', 'latestSubscription', 'plan']);


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

    public function closeModalUpdatePassword()
    {
        $this->closeModal('updatePasswordModal');
    }

    public function changePassword()
    {
        $this->form->changePassword();
        $this->closeModalUpdatePassword();

        session()->flash('success', 'Password has been changed');
    }

    public function customSave()
    {
        try {
            $this->save();
        } catch (\Exception $e) {
            $this->dispatch('alert', type: Alert::error->value, message: $e->getMessage());
        }
    }
}
