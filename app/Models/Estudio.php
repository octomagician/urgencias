<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estudio extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'tipos_de_estudios_id',
        'personal_id',
    ];

    public function tipos_de_estudio()
    {
        return $this->hasOne(Tipos_de_estudio::class);
    }

    public function personal()
    {
        return $this->hasOne(Personal::class);
    }
}
