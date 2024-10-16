<?php

namespace Database\Seeders;

use App\Models\Bandwidth;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BandwidthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bandwidth::create([
            'name' => 'Bandwidth 1',
            'rate_download' => 50,
            'rate_download_unit' => 'Mbps',
            'rate_upload' => 50,
            'rate_upload_unit' => 'Mbps',
            'burst' => '100 100 50 1 200',
            'star' => '*14'
        ]);

        Bandwidth::create([
            'name' => 'Bandwidth 2',
            'rate_download' => 100,
            'rate_download_unit' => 'Mbps',
            'rate_upload' => 100,
            'rate_upload_unit' => 'Mbps',
            'burst' => '100 100 50 1 200',
            'star' => '*14'
        ]);
    }
}
