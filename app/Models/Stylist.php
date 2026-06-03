<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stylist extends Model
{
    protected $fillable = [
        'name',
        'location',
        'specialist_1',
        'specialist_2',
        'specialist_3',
        'image',
    ];
}
