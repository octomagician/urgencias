<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'persona_id', 'nacimiento', 'nss', 'direccion', 'tel_1', 'tel_2',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function ingreso()
    {
        return $this->hasMany(Ingreso::class);
    }
}