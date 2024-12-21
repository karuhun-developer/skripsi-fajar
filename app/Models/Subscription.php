<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'start_at',
        'expired_at',
        'status',
        'invoice_generated_at',
    ];
    protected $casts = [
        'start_at' => 'datetime', // Konversi otomatis ke Carbon
        'expired_at' => 'datetime', // Konversi otomatis ke Carbon
        'invoice_generated_at' => 'datetime', // Jika perlu
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function scopeDue($query)
    {
        return $query->whereDate('start_at', '<=', DB::raw('CURDATE()'))
            ->whereNull('invoice_generated_at');
    }
}
