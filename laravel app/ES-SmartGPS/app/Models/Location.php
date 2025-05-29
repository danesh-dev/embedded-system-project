<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

        protected $fillable = [
        'lat',
        'long',
        'date',
        'deviceId'
    ];

    protected $casts = [
        'date' => 'datetime',
        'lat' => 'decimal:8',
        'long' => 'decimal:8'
    ];

    public function getGoogleMapsUrlAttribute()
    {
        return "https://www.google.com/maps?q={$this->lat},{$this->long}";
    }
}
