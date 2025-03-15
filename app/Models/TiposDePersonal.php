<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TiposDePersonal extends Model
{
    use SoftDeletes, HasFactory;
    
    protected $fillable = ['nombre'];
    protected $table = 'tipos_de_personal';

    public function user()
    {
        return $this->hasMany(User::class, 'tipo_id');
    }
}
