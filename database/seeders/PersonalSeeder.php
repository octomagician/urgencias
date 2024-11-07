<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;
use App\Models\Persona;

use App\Models\TiposDePersonal;
use App\Models\Personal;

class PersonalSeeder extends Seeder
{
    public function run()
    {
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('User'); 
            
            $persona = Persona::factory()->create([
                'users_id' => $user->id,
            ]);
    
            Personal::factory()->create([
                'persona_id' => $persona->id,
            ]);
        });
    }
}