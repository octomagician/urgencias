<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $guest = Role::create(['name' => 'Guest']);
        $user = Role::create(['name' => 'User']);
        $admin = Role::create(['name' => 'Administrador']);

        Permission::create(['name' => 'ver perfil']);
        Permission::create(['name' => 'editar perfil']);
        Permission::create(['name' => 'subir foto de perfil']);
        Permission::create(['name' => 'authorize roles']); 

        $user->givePermissionTo(['ver perfil', 'editar perfil', 'subir foto de perfil']);
        $admin->givePermissionTo(Permission::all());

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('Administrador');
    }
}

