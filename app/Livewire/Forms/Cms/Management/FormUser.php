<?php

namespace App\Livewire\Forms\Cms\Management;

use App\Livewire\Contracts\FormCrudInterface;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Livewire\Form;
use RouterOS\Client;
use RouterOS\Query;

class FormUser extends Form implements FormCrudInterface
{
    public $id;

    public $role;

    public $name;

    public $email;

    public $password;

    public $pppoeUsername;

    public $pppoePassword;

    public $localIp;
    public $planId;

    // Get the data
    public function getDetail($id)
    {
        $data = User::find($id);

        $this->id = $id;
        $this->name = $data->name;
        $this->email = $data->email;
        $this->role = $data->getRoleNames()[0];
    }

    // Save the data
    public function save()
    {
        if ($this->id) {
            $this->update();
        } else {
            $this->store();
        }

        $this->reset();
    }

    // Store data
    public function store()
    {
//        $this->validate([
//            'id' => 'nullable',
//            'role' => 'required',
//            'name' => 'required',
//            'email' => 'required',
//            'password' => 'required|unique:users,email',
//            'pppoeUsername' => 'required',
//            'pppoePassword' => 'required',
//            'localIp' => 'required',
//        ]);
        if ($this->role != 'admin'){
            $plan = Plan::find($this->planId);
            $router = $plan->pool->router;
            $client = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => 8728,
            ]);

            $query = new Query('/ppp/secret/add');
            $query->equal('name', $this->pppoeUsername);
            $query->equal('password', $this->pppoePassword);
            $query->equal('service', 'pppoe');
            $query->equal('profile', $plan->name);
            $response = $client->query($query)->read();

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'pppoe_username' => $this->pppoeUsername,
                'pppoe_password' => $this->pppoePassword,
                'pppoe_ip' => $this->localIp,
                'plan_id' => $this->planId,
                'star' => $response['after']['ret'],
            ]);

            // Assign new role
            $user->assignRole($this->role);

            $startAt = now();
            switch ($plan->validity_unit) {
                case 'day':
                    $expiredAt = $startAt->copy()->addDays($plan->validity);
                    break;
                case 'month':
                    $expiredAt = $startAt->copy()->addMonths($plan->validity);
                    break;
                case 'year':
                    $expiredAt = $startAt->copy()->addYears($plan->validity);
                    break;
                default:
                    throw new \Exception('Invalid validity unit');
            }

            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $this->planId,
                'start_at' => $startAt,
                'expired_at' => $expiredAt,
                'status' => 'active',
            ]);
        }
    }

    // Update data
    public function update()
    {
        $this->validate([
            'id' => 'required',
            'role' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$this->id,
        ]);

        $user = User::find($this->id);

        // Remove all role
        $user->syncRoles([]);

        // Assign new role
        $user->assignRole($this->role);
        $user->update($this->only([
            'name',
            'email',
        ]));
    }

    // Delete data
    public function delete($id)
    {
        User::find($id)->delete();
    }

    // Change password
    public function changePassword()
    {
        User::find($this->id)->update([
            'password' => $this->password,
        ]);
    }
}
