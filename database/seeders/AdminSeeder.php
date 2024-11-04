<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name' => 'Admin', 
            'role' => ('Administrador'),
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('panecito'),
            'remember_token' => Str::random(10),
        ]);
    }
}
