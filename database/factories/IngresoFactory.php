<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Diagnostico;
use App\Models\Paciente;
use App\Models\Cama;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingresos>
 */
class IngresoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'pacientes_id' => Paciente::inRandomOrder()->first()->id,
            'diagnostico_id' => Diagnostico::inRandomOrder()->first()->id,
            'camas_id' => Cama::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'fecha_ingreso' => $this->faker->date(),
            'motivo_ingreso' => $this->faker->text(100),
            'fecha_alta' => $this->faker->optional()->date(),
        ];
    }
}
