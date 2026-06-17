<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'home_price' => 'decimal:2',
        'stopdesk_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
