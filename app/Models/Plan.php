<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    /** @use HasFactory<\Database\Factories\PlanFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'bandwidth_id',
        'pool_id',
        'validity',
        'validity_unit',
        'type',
        'star',
    ];

    public function bandwidth()
    {
        return $this->belongsTo(Bandwidth::class);
    }

    public function pool()
    {
        return $this->belongsTo(Pool::class);
    }
}
