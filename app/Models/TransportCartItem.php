<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportCartItem extends Model
{
    protected $fillable = [
        'user_id',
        'guest_id',
        'from_city_id',
        'to_city_id',
        'item_name',
        'item_type',
        'quantity',
        'length_cm',
        'width_cm',
        'height_cm',
        'weight_kg',
        'pickup_date',
        'delivery_date',
        'estimated_total',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'delivery_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromCity()
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    public function toCity()
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }
}
