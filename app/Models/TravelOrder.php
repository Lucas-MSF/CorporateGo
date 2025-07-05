<?php

namespace App\Models;

use App\Scopes\AuthUserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelOrder extends Model
{
    /** @use HasFactory<\Database\Factories\TravelOrderFactory> */
    use HasFactory;

    protected $table = 'travel_orders';
    protected $fillable = [
        'requestor_name',
        'requestor_id',
        'destination',
        'departure_date',
        'return_date',
        'status',
    ];

    protected $casts = [
        'departure_date' => 'date:Y-m-d',
        'return_date' => 'date:Y-m-d',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requestor_id', 'id');
    }
    protected static function booted(): void
    {
        static::addGlobalScope(new AuthUserScope(field: 'requestor_id'));
    }
}
