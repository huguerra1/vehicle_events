<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
     protected $fillable = [
        'user_id',
        'make',
        'model',
        'year',
        'license_plate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceRecords()
    {
        return $this->hasMany(ServiceRecord::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_vehicle')
            ->withPivot('registration_date')
            ->withTimestamps();
    }
}
