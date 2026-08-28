<?php

namespace App\Models;


use App\Models\Vehicle;
use App\Models\Event;
use App\Models\UserProfile;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;

/**
 * Model de usuarios
 *
 * @author Hugo Fonseca Mourao <hugo.mourao@sysout.com.br>
 * @date 2026-08-27
 */
class User extends Authenticatable implements OAuthenticatable
{
     use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password_hash',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Relacionamento de 1 pra 1 entre usuario e perfil
     *
     * @return void
     * @author Hugo Fonseca Mourao <hugo.mourao@sysout.com.br>
     * @date 2026-08-27
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Relacionamento 1 pra n entre usuarios e veiculos
     *
     * @return void
     * @author Hugo Fonseca Mourao <hugo.mourao@sysout.com.br>
     * @date 2026-08-27
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Relacionamento de 1 pra n entre usuario e eventos
     *
     * @return void
     * @author Hugo Fonseca Mourao <hugo.mourao@sysout.com.br>
     * @date 2026-08-27
     */
    public function eventsOrganized()
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }
}
