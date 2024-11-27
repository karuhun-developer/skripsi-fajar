<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use RouterOS\Query;
use App\Models\Pool;
use RouterOS\Client;
use App\Models\Bandwidth;
use App\Models\Plan as PlanModel;
use Livewire\Attributes\Validate;
use App\Livewire\Contracts\FormCrudInterface;
use App\Models\DowngradedBandwidth;

class FormPppoe extends Form implements FormCrudInterface
{
    #[Validate('nullable|numeric')]
    public $id;

    #[Validate('required')]
    public $name;

    // #[Validate('required')]
    public $bandwitdh;

    #[Validate('required')]
    public $price;

    // #[Validate('required')]
    public $poolId;

    #[Validate('required')]
    public $validity;

     #[Validate('required')]
    public $validityUnit;

     #[Validate('required')]
    public $bandwidthId;
    public $type = 'PPPOE';

    // Get the data
    public function getDetail($id)
    {
        $this->id = $id;
        $data = PlanModel::find($id);
        $this->name = $data->name;
        $this->bandwidthId = $data->bandwidth_id;
        $this->poolId = $data->pool_id;
        $this->price = numberToCurrency($data->price);
        $this->validity = $data->validity;
        $this->validityUnit = $data->validity_unit;
        $this->type = $data->type;
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
            $dataBandwidth = Bandwidth::where('id', $this->bandwidthId)->first();
            $dataPool = Pool::where('id', $this->poolId)->first();
            $rateLimit = $dataBandwidth->rate_download . 'M/' . $dataBandwidth->rate_upload . 'M ' . $dataBandwidth->burst;
            $rateLimitDowngrade = $dataBandwidth->downgradedBandwidth->rate_download . 'M/' . $dataBandwidth->downgradedBandwidth->rate_upload . 'M ' . $dataBandwidth->burst;

            $client = new Client([
                'host' => $dataPool->router->ip_address,
                'user' => $dataPool->router->username,
                'pass' => $dataPool->router->password,
            ]);


            $query = new Query('/ppp/profile/add');
            $query->equal('name', $this->name);
            $query->equal('local-address', $dataPool->local_ip);
            $query->equal('remote-address', $dataPool->name);
            $query->equal('rate-limit', $rateLimit);
            $response = $client->query($query)->read();

            if (isset($response['after']['message'])) {
                throw new \Exception($response['after']['message']);
            }

            $star = $response['after']['ret'];

            //create downgraded plan
            $query = new Query('/ppp/profile/add');
            $query->equal('name', $this->name . '_DOWNGRADE_50');
            $query->equal('local-address', $dataPool->local_ip);
            $query->equal('remote-address', $dataPool->name);
            $query->equal('rate-limit', $rateLimitDowngrade);
            $response = $client->query($query)->read();
            $starDowngrade = $response['after']['ret'];

            if ($response['after']['ret'] != null) {
                PlanModel::create([
                    'name' => $this->name,
                    'bandwidth_id' => $this->bandwidthId,
                    'pool_id' => $this->poolId,
                    'price' => currencyToNumber($this->price),
                    'validity' => $this->validity,
                    'validity_unit' => $this->validityUnit,
                    'type' => $this->type,
                    'star' => $star,
                    'star_downgrade' => $starDowngrade,
                ]);
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // Update data
    public function update()
    {
        try {
            $dataBandwidth = Bandwidth::where('id', $this->bandwidthId)->first();
            $dataPool = Pool::where('id', $this->poolId)->first();
            $rateLimit = $dataBandwidth->rate_download . 'M/' . $dataBandwidth->rate_upload . 'M ' . $dataBandwidth->burst;

            $client = new Client([
                'host' => $dataPool->router->ip_address,
                'user' => $dataPool->router->username,
                'pass' => $dataPool->router->password,
            ]);

            $query = new Query('/ppp/profile/set');
            $query->equal('name', $this->name);
            $query->equal('local-address', $dataPool->local_ip);
            $query->equal('remote-address', $dataPool->name);
            $query->equal('rate-limit', $rateLimit);
            $response = $client->query($query)->read();

            if (isset($response['after']['message'])) {
                throw new \Exception($response['after']['message']);
            }

            PlanModel::find($this->id)->update([
                'name' => $this->name,
                'bandwidth_id' => $this->bandwidthId,
                'pool_id' => $this->poolId,
                'price' => $this->price,
                'validity' => $this->validity,
                'validity_unit' => $this->validityUnit,
                'type' => $this->type,
            ]);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    // Delete data
    public function delete($id)
    {
        try {


            $data = PlanModel::find($id);
            $dataPool = Pool::where('id', $data->pool_id)->first();
            $client = new Client([
                'host' => $dataPool->router->ip_address,
                'user' => $dataPool->router->username,
                'pass' => $dataPool->router->password,
            ]);

            $query = new Query('/ppp/profile/remove');
            $query->equal('.id', $data->star);

            //star downgrade
            $query2 = new Query('/ppp/profile/remove');
            $query2->equal('.id', $data->star_downgrade);

            $response = $client->query($query)->query($query2)->read();

            if (isset($response['after']['message'])) {
                throw new \Exception("Profile not found");
            }

            PlanModel::find($id)->delete();

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }
}
