<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Log extends Model
{
    protected $connection = 'mongodb'; // Usa la conexión de MongoDB
    protected $collection = 'logs';   // Nombre de la colección en MongoDB

    protected $fillable = [
        'action',
        'user_id',
        'details',
    ];
}