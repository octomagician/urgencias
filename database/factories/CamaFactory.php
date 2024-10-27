<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Persona;
use App\Models\Paciente;
use App\Models\TiposDePersonal;
use App\Models\Personal;
use App\Models\TiposDeEstudio;
use App\Models\Area;
use App\Models\Ingreso;
use App\Models\Estudio;
use App\Models\Historial;
use App\Models\Cama;
use App\Models\Diagnostico;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Camas>
 */
class CamaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'numero_cama' => $this->faker->unique()->numberBetween(1, 100),
            'area_id' => Area::inRandomOrder()->first()->id,
        ];
    }
}
