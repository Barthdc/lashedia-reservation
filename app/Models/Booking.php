<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
  protected $fillable = [

    'user_id',

    'date',
    'time',
    'stylist',

    'name',
    'email',
    'phone',

    'service',

    'payment_method',
    'payment_proof',

    'note',

    'status',

];
    public function user()
{
    return $this->belongsTo(User::class);
}

public function stylist()
{
    return $this->belongsTo(Stylist::class);
}
}
