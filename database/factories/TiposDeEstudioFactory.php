<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TiposDeEstudio;
use App\Models\Estudio;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tipos_de_estudios>
 */
class TiposDeEstudioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $tiposPosibles = [
            'Exploratorios',
            'Invasivos',
            'Laboratorio',
            'Funcionales',
            'Radiológicos',
            'Endoscópicos',
            'Electrofisiológicos',
            'Molecular',
            'Imagenológicos',
            'Histopatológicos',
            'Psicológicos',
            'Genéticos',
            'Clínicos',
            'Fisiológicos',
            'Diagnóstico por imagen',
            'Ultrasonido',
            'Resonancia magnética',
            'Tomografía computarizada',
            'Biopsias',
            'Exámenes de sangre',
            'Pruebas de orina',
            'Estudios de función pulmonar',
            'Estudios cardiovasculares',
            'Pruebas de alergia',
            'Estudios de diagnóstico por laboratorio',
            'Exámenes dermatológicos',
            'Evaluación neurológica',
            'Pruebas de diagnóstico por imagen',
            'Evaluaciones nutricionales',
            'Estudios oncológicos'
        ];

        return [
            'nombre' => $this->faker->randomElement($tiposPosibles),
        ];
    }
}
