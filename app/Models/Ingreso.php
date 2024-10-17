<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    use HasFactory;

    protected $fillable = [
        'pacientes_id',
        'diagnostico_id',
        'camas_id',
        'personal_id',
        'fecha_ingreso',
        'motivo_ingreso',
        'fecha_alta',
    ];

    public function historial()
    {
        return $this->hasMany(Historial::class);
    }

    public function cama()
    {
        return $this->hasOne(Cama::class);
    }

    public function paciente()
    {
        return $this->hasOne(Paciente::class);
    }

    public function diagnostico()
    {
        return $this->hasOne(Diagnostico::class);
    }
}
