<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',

        'name',
        'email',
        'phone',

        'full_address',
        'village',
        'district',
        'city',
        'province',
        'island',
        'latitude',
        'longitude',
        'shipping_zone',
        'shipping_cost',
        'flight_ticket_cost',
        'total_location_cost',

        'service',
        'date',
        'time',
        'note',
        'stylist',
        'payment_method',
        'payment_proof',
        'status',
    ];
}
