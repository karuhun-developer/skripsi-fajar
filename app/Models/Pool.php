<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    /** @use HasFactory<\Database\Factories\PoolFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'local_ip',
        'range_ip',
        'star',
        'router_id',
        'description'
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }
}
