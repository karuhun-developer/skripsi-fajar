<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DowngradedBandwidth extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'bandwidth_id',
        'rate_download',
        'rate_download_unit',
        'rate_upload',
        'rate_upload_unit',
        'burst',
        'star',
    ];
}
