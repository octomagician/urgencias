<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'nombre', 'apellido_paterno', 'apellido_materno', 'sexo', 'users_id',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function paciente()
    {
        return $this->hasOne(Paciente::class);
    }

    public function personal()
    {
        return $this->hasOne(Personal::class);
    }
}
