<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    use HasFactory;

    protected $fillable = ['dx', 'estatus'];

    public function ingreso()
    {
        return $this->hasMany(Ingreso::class);
    }
}
