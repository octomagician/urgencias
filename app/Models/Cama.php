<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cama extends Model
{
    use HasFactory;
    use SoftDeletes;

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
