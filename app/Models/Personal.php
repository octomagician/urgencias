<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personal extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['persona_id', 'tipo_id'];
    protected $table = 'personal';

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
