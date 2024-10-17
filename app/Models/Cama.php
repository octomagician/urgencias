<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cama extends Model
{
    use HasFactory;

    protected $fillable = ['numero_cama', 'area_id'];
    
    public function ingreso()
    {
        return $this->hasMany(Ingreso::class);
    }

    public function area()
    {
        return $this->hasOne(Area::class);
    }
}
