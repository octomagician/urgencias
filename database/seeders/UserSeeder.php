<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Persona;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Crear 30 usuarios con sus personas asociadas
        Persona::factory(30)->create()->each(function ($persona) {
            $user = User::factory()->create([
                'persona_id' => $persona->id,
            ]);

            // Verificar si el usuario es el administrador
            if ($user->email !== 'admin@example.com') {
                // Asignar el rol de "User" solo si no es el administrador
                $user->syncRoles('User');
            }
        });
    }
}