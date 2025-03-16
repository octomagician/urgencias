<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        return null;
        // si no hay token, está vencido o inválido, me manda al Handler
    }
}
