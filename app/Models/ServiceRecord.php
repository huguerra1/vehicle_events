<?php

namespace App\Models;

use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ServiceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'service_type',
        'description',
        'service_date',
        'cost',
    ];

    protected $casts = [
        'service_type' => ServiceType::class,
        'service_date' => 'date',
        'cost'         => 'decimal:2',
    ];


    /**
     * Relacionamento de 1 pra 1 entre veiculo e serviço
     *
     * @return void
     * @author Hugo Fonseca Mourao <hugo.mourao@sysout.com.br>
     * @date 2026-08-27
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
