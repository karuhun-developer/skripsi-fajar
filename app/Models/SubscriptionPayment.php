<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'bill_amount',
        'payment_link',
        'order_id',
        'status',
        'created_at',
        'updated_at',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
