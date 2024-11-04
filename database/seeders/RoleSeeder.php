<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        $user->givePermissionTo(['ver perfil', 'editar perfil', 'subir foto de perfil']);
        $admin->givePermissionTo(Permission::all());
    }
}

