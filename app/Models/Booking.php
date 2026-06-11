<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',

        'name',
        'email',
        'phone',

        'full_address',
        'latitude',
        'longitude',
        'mua_latitude',
        'mua_longitude',
        'distance_km',
        'transport_cost',
        'transport_note',

        'service',
        'date',
        'time',
        'note',
        'stylist',
        'payment_method',
        'payment_proof',
        'status',
        'reject_reason',

        'invoice_number',
        'invoice_date',
        'invoice_subtotal',
        'invoice_transport',
        'invoice_total',
        'invoice_sent_at',
    ];
}
