<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Middlewares\RoleMiddleware;

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = ['persona_id', 
    'tipo_id',
    'username',
    'email',
    'password',
    'profile_photo_path',];

    protected $table = 'users';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function tipoDePersonal()
    {
        return $this->belongsTo(TiposDePersonal::class, 'tipo_id');
    }

    public function historial()
    {
        return $this->hasMany(Historial::class);
    }

    public function estudio()
    {
        return $this->hasMany(Estudio::class);
    }
}
