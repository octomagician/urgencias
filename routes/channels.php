<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal público para actualizaciones de historial
// No requiere autenticación específica
Broadcast::channel('historial', function ($user) {
    // Si quieres limitar el acceso, puedes añadir condiciones aquí
    // Por ejemplo, solo usuarios autenticados:
    return !is_null($user);
    
    // O para permitir a todos (incluso no autenticados):
    // return true;
});
