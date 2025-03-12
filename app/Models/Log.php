<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $connection = 'mongodb'; // Usa la conexión de MongoDB
    protected $collection = 'logs';   // Nombre de la colección en MongoDB

    protected $fillable = [
        'accion',
        'users_id',
        'detalle',
    ];
}
