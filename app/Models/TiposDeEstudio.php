<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposDeEstudio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
    ];
    public function estudio()
    {
        return $this->hasMany(Estudio::class);
    }
}
