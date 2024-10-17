<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Paciente;
use App\Models\Persona;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paciente>
 */
class PacienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'persona_id' => Personas::factory(),
            'nacimiento' => $this->faker->date(),
            'nss' => $this->faker->unique()->numerify('###########'),
            'direccion' => $this->faker->address(),
            'tel_1' => $this->faker->phoneNumber(),
            'tel_2' => $this->faker->optional()->phoneNumber(),
        ]; 
    }
}
