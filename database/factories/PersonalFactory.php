<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Historial;
use App\Models\Personal;
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
        return [
            'persona_id' => Persona::factory(), 
            'tipo_id' => TiposDePersonal::factory(),
        ];
    }
}
