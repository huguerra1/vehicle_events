<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'event_name',
        'event_date',
        'location',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function scopeSearch($query, Request $request)
    {
        if ($request->search) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->orWhere('event_name', 'ilike', "%{$search}%");
                $q->orWhere('location', 'ilike', "%{$search}%");
            });
        }
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'event_vehicle')
            ->withPivot('registration_date');

    }
}
