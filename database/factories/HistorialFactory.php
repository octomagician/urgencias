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
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Historial>
 */
class HistorialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ingreso_id' => Ingreso::inRandomOrder()->first()->id,
            'personal_id' => Personal::inRandomOrder()->first()->id,
            'fecha_registro' => $this->faker->dateTime(),
            'presion' => $this->faker->randomElement(['120/80', '130/85', '140/90', '110/70']),
            'temperatura' => $this->faker->randomFloat(2, 35, 42),
            'glucosa' => $this->faker->randomFloat(2, 70, 180),
            'sintomatologia' => $this->faker->text(200),
            'observaciones' => $this->faker->optional()->text(100),
        ];
    }
}
