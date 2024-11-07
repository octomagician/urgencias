<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Persona;
use App\Models\Paciente;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PacientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory(30)->create()->each(function ($user) {
            $user->assignRole('User'); 
            
            $persona = Persona::factory()->create([
                'users_id' => $user->id,
            ]);

            Paciente::factory()->create([
                'persona_id' => $persona->id,
            ]);
        });
    }
}
