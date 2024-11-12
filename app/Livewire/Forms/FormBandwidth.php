<?php

namespace App\Livewire\Forms;

use App\Livewire\Contracts\FormCrudInterface;
use App\Models\Bandwidth;
use App\Models\DowngradedBandwidth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class FormBandwidth extends Form implements FormCrudInterface
{
    #[Validate('nullable|numeric')]
    public $id;

    #[Validate('required')]
    public $name;
    #[Validate('required')]
    public $rateDownload;
    #[Validate('required')]
    public $rateDownloadUnit;
    #[Validate('required')]
    public $rateUpload;
    #[Validate('required')]
    public $rateUploadUnit;
    #[Validate('required')]
    public $burst;

    // Get the data
    public function getDetail($id) {
        $data = Bandwidth::find($id);
        $this->id = $id;
        $this->name = $data->name;
        $this->rateDownload = $data->rate_download;
        $this->rateDownloadUnit = $data->rate_download_unit;
        $this->rateUpload = $data->rate_upload;
        $this->rateUploadUnit = $data->rate_upload_unit;
        $this->burst = $data->burst;
    }

    // Save the data
    public function save() {
        $this->validate();

        if ($this->id) {
            $this->update();
        } else {
            $this->store();
        }

        $this->reset();
    }

    // Store data
    public function store() {
        $this->validate();
        $badnwidth = Bandwidth::create([
            'name' => $this->name,
            'rate_download' => $this->rateDownload,
            'rate_download_unit' => $this->rateDownloadUnit,
            'rate_upload' => $this->rateUpload,
            'rate_upload_unit' => $this->rateUploadUnit,
            'burst' => $this->burst,
            'star' => "*0",
        ]);
        DowngradedBandwidth::create([
            "name"=> $this->name . "_DOWNGRADE_50",
            'bandwidth_id' => $badnwidth->id,
            'rate_download' => $this->rateDownload * 0.5,
            'rate_download_unit' => $this->rateDownloadUnit,
            'rate_upload' => $this->rateUpload * 0.5,
            'rate_upload_unit' => $this->rateUploadUnit,
            'burst' => $this->burst
        ]);
    }

    // Update data
    public function update() {
        Bandwidth::find($this->id)->update([
            'name' => $this->name,
            'rate_download' => $this->rateDownload,
            'rate_download_unit' => $this->rateDownloadUnit,
            'rate_upload' => $this->rateUpload,
            'rate_upload_unit' => $this->rateUploadUnit,
            'burst' => $this->burst,
        ]);
    }

    // Delete data
    public function delete($id) {
        Bandwidth::find($id)->delete();
    }
}
