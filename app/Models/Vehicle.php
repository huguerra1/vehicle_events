<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Vehicle extends Model
{
     use HasFactory;

     protected $fillable = [
        'user_id',
        'brand',
        'model',
        'year',
        'plate',
    ];

    public function scopeSearch($query, Request $request)
    {
        if ($request->search) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->orWhere('brand', 'ilike', "%{$search}%");
                $q->orWhere('model', 'ilike', "%{$search}%");
                $q->orWhere('plate', 'ilike', "%{$search}%");
            });
        }
    }

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
            ->withPivot('registration_date');
    }
}
