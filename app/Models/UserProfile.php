<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Model do perfil de usuario
 *
 * @author Hugo Fonseca Mourao <hugo.mourao@sysout.com.br>
 * @date 2026-08-27
 */
class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'instagram_handle',
        'phone',
    ];

    /**
     * Relacionamento de 1 pra 1 entre perfil e usuario
     *
     * @return void
     * @author Hugo Fonseca Mourao <hugo.mourao@sysout.com.br>
     * @date 2026-08-27
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
