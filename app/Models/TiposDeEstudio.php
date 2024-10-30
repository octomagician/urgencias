<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TiposDeEstudio extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'nombre',
    ];
    public function estudio()
    {
        return $this->hasMany(Estudio::class);
    }
}
