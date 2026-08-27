<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'organizer_id',
        'event_name',
        'event_date',
        'location',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'event_vehicle')
            ->withPivot('registration_date')
            ->withTimestamps();
    }
}
