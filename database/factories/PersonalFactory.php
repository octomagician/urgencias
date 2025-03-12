<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Persona;
use App\Models\TiposDePersonal;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Personal>
 */
class PersonalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $tipoDePersonal = TiposDePersonal::inRandomOrder()->first();

        return [
            'persona_id' => Persona::factory(), // Crea una nueva persona o usa una existente
            'tipo_id' => $tipoDePersonal ? $tipoDePersonal->id : TiposDePersonal::factory(), // Usa un tipo existente o crea uno nuevo si no hay
        ];
    }
}