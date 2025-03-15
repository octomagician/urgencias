<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TiposDePersonal;

class TiposDePersonalFactory extends Factory
{
    public function definition()
    {
        $nombresPosibles = ['Doctor', 'Enfermero', 'Administrativo', 'Técnico', 'Recepcionista'];

        return [
            'nombre' => $this->faker->randomElement($nombresPosibles),
        ];
    }
}
