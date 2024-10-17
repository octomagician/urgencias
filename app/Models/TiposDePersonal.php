<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposDePersonal extends Model
{
    use HasFactory;
    
    protected $fillable = ['nombre'];
    protected $table = 'tipos_de_personal';

    public function personal()
    {
        return $this->hasMany(Personal::class, 'tipo_id');
    }
}
