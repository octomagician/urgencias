<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diagnostico extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['dx', 'estatus'];

    public function ingreso()
    {
        return $this->hasMany(Ingreso::class);
    }
}
