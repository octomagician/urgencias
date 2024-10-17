<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingreso_id',
        'personal_id',
        'presion',
        'temperatura',
        'glucosa',
        'sintomatologia',
        'observaciones',
    ];

    public function ingreso()
    {
        return $this->hasOne(Ingreso::class);
    }

    public function personal()
    {
        return $this->hasOne(Personal::class);
    }
}
