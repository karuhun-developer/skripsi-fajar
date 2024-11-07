<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bandwidth extends Model
{
    /** @use HasFactory<\Database\Factories\BandwidthFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'rate_download',
        'rate_download_unit',
        'rate_upload',
        'rate_upload_unit',
        'burst',
        'star',
    ];
}
