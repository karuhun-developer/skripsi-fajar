<?php

namespace App\Livewire\Forms;

use App\Services\MikrotikService;
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

    public $routerId;

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

    public $description;
    private $mikrotikService;

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
        $this->description = $data->description;

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
        $bandwidth = Bandwidth::where('id', $this->bandwidthId)->first();
        $pool = Pool::where('id', $this->poolId)->first();
        $rateLimit = $bandwidth->rate_download . 'M/' . $bandwidth->rate_upload . 'M ' . $bandwidth->burst;
        $rateLimitDowngrade = $bandwidth->downgradedBandwidth->rate_download . 'M/' . $bandwidth->downgradedBandwidth->rate_upload . 'M ' . $bandwidth->burst;

        $this->mikrotikService = new MikrotikService($pool->router->id);
        $response = $this->mikrotikService->createPppoeProfile(
            $this->name,
            $pool->local_ip,
            $pool->name,
            $rateLimit,
            $this->description);

        if (isset($response['after']['message'])) {
            throw new \Exception($response['after']['message']);
        }

        $star = $response['after']['ret'];

        //create downgraded plan
        $response2 = $this->mikrotikService->createPppoeProfile(
            $this->name . '_DOWNGRADE',
            $pool->local_ip,
            $pool->name,
            $rateLimitDowngrade,
            $this->description);

        if (isset($response2['after']['message'])) {
            throw new \Exception($response2['after']['message']);
        }

        $starDowngrade = $response2['after']['ret'];

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
            'description' => $this->description,
        ]);
    }

    // Update data
    public function update()
    {
        $dataBandwidth = Bandwidth::where('id', $this->bandwidthId)->first();
        $dataPool = Pool::where('id', $this->poolId)->first();
        $plan = PlanModel::find($this->id);
        $rateLimit = $dataBandwidth->rate_download . 'M/' . $dataBandwidth->rate_upload . 'M ' . $dataBandwidth->burst;
        $rateLimitDowngrade = $dataBandwidth->downgradedBandwidth->rate_download . 'M/' . $dataBandwidth->downgradedBandwidth->rate_upload . 'M ' . $dataBandwidth->burst;

        $this->mikrotikService = new MikrotikService(
            $dataPool->router->username,
            $dataPool->router->password,
            $dataPool->router->ip_address,
            $dataPool->router->port
        );
        $response = $this->mikrotikService->updatePppoeProfile(
            $plan->star,
            $this->name,
            $dataPool->local_ip,
            $dataPool->name,
            $rateLimit,
            $this->description);

        if (isset($response['after']['message'])) {
            throw new \Exception($response['after']['message']);
        }

        //update downgrade plan
        $response2 = $this->mikrotikService->updatePppoeProfile(
            $plan->star_downgrade,
            $this->name . '_DOWNGRADE',
            $dataPool->local_ip,
            $dataPool->name,
            $rateLimitDowngrade,
            $this->description);

        if (isset($response2['after']['message'])) {
            throw new \Exception($response2['after']['message']);
        }

        PlanModel::find($this->id)->update([
            'name' => $this->name,
            'bandwidth_id' => $this->bandwidthId,
            'pool_id' => $this->poolId,
            'price' => $this->price,
            'validity' => $this->validity,
            'validity_unit' => $this->validityUnit,
            'type' => $this->type,
            'description' => $this->description,
        ]);

    }

    // Delete data
    public function delete($id)
    {

        $data = PlanModel::find($id);
        $dataPool = Pool::where('id', $data->pool_id)->first();

        $this->mikrotikService = new MikrotikService(
            $dataPool->router->username,
            $dataPool->router->password,
            $dataPool->router->ip_address,
            $dataPool->router->port
        );

        $this->mikrotikService->deletePppoeProfile($data->star);
        $this->mikrotikService->deletePppoeProfile($data->star_downgrade);
        PlanModel::find($id)->delete();

    }
}
