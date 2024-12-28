<?php

namespace App\Livewire\Forms;

use App\Livewire\Contracts\FormCrudInterface;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\MikrotikService;
use Livewire\Attributes\Validate;
use Livewire\Form;
use RouterOS\Client;
use RouterOS\Query;

class FormCustomer extends Form implements FormCrudInterface
{
    #[Validate('nullable|numeric')]
    public $id;
    public $role = 'customer';

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
        $this->planId = $data->plan_id;
        $this->pppoeUsername = $data->pppoe_username;
        $this->pppoePassword = $data->pppoe_password;
    }

    // Save the data
    public function save()
    {
        $this->validate();

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
        try {
            if ($this->role != 'admin') {
                $plan = Plan::find($this->planId);
                $router = $plan->pool->router;

                $mikrotikService = new MikrotikService($plan->pool->router);
                $response = $mikrotikService->executeQuery('/ppp/secret/add', [
                    'name' => $this->pppoeUsername,
                    'password' => $this->pppoePassword,
                    'service' => 'pppoe',
                    'profile' => $plan->name,
                ]);

                if (isset($response['after']['message'])) {
                    throw new \Exception($response['after']['message']);
                }

                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => $this->password,
                    'pppoe_username' => $this->pppoeUsername,
                    'pppoe_password' => $this->pppoePassword,
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
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    public function update()
    {
        $this->validate([
            'id' => 'required',
            'role' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $this->id,
        ]);

        $user = User::find($this->id);

        // Remove all roles
        $user->syncRoles([]);

        // Assign new role
        $user->assignRole($this->role);

        // Update user details
        $user->update($this->only([
            'name',
            'email',
        ]));

        // Additional logic for customer type
        $this->validate([
            'planId' => 'required',
            'pppoeUsername' => 'required',
            'pppoePassword' => 'required',
        ]);

        $plan = Plan::find($this->planId);
        $router = $plan->pool->router;

        $mikrotikService = new MikrotikService($plan->pool->router);
        $response = $mikrotikService->executeQuery('/ppp/secret/set', [
            '.id' => $user->star,
            'name' => $this->pppoeUsername,
            'password' => $this->pppoePassword,
            'profile' => $plan->name,
        ]);

        if (isset($response['after']['message'])) {
            throw new \Exception($response['after']['message']);
        }

        $user->update([
            'pppoe_username' => $this->pppoeUsername,
            'pppoe_password' => $this->pppoePassword,
            'plan_id' => $this->planId,
        ]);

        $subscription = Subscription::where('user_id', $user->id)->first();
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

        $subscription->update([
            'plan_id' => $this->planId,
            'start_at' => $startAt,
            'expired_at' => $expiredAt,
            'status' => 'active',
        ]);

    }

    public function delete($id)
    {
        $user = User::find($id);
        $router = $user->plan->pool->router;
        try {

            $mikrotikService = new MikrotikService($user->plan->pool->router);
            $response = $mikrotikService->executeQuery('/ppp/secret/remove', [
                '.id' => $user->star,
            ]);

            //delete from database
            if ($response == null) {
                $user->delete();
            }

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function changePassword()
    {
        User::find($this->id)->update([
            'password' => $this->password,
        ]);
    }
}
