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
    public function run()
    {
        // Crear 30 personas
        Persona::factory(30)->create()->each(function ($persona) {
            // Crear un paciente asociado a la persona
            Paciente::factory()->create([
                'persona_id' => $persona->id,
            ]);
        });
    }
}
