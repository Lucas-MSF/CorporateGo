<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
