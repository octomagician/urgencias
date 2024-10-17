<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TiposDePersonal;
use App\Models\Personal;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipoDePersonal>
 */
class TiposDePersonalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $nombresPosibles = ['Doctor', 'Enfermero', 'Administrativo', 'Técnico', 'Recepcionista'];

        return [
            'nombre' => $this->faker->randomElement($nombresPosibles),
        ];
    }
}
