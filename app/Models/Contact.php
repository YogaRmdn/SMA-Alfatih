<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'phone',
        'whatsapp',
        'email',
        'maps_embed',
        'operational_hours',
        'latitude',
        'longitude',
    ];
}
