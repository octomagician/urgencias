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
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Area>
 */
class AreaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $areasPosibles = ['Triaje', 'Recepción', 'Evaluación Inicial', 'Observación', 'Emergencia'
        , 'Radiología', 'Tratamiento', 'Recuperación', 'Especialidades', 'Consultas Externas'];

        return [
            'nombre' => $this->faker->randomElement($areasPosibles),
        ];
    }
}
