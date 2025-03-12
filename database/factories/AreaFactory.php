<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Area;
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
        $areasPosibles = [
            'Triaje', 
            'Recepción', 
            'Evaluación Inicial', 
            'Observación', 
            'Emergencia', 
            'Radiología', 
            'Tratamiento', 
            'Recuperación', 
            'Especialidades', 
            'Consultas Externas',
            'Laboratorio', 
            'Medicina Nuclear', 
            'Farmacia', 
            'Medicina Física', 
            'Atención Urgente', 
            'Clínica de Ojos', 
            'Clínicas Médicas', 
            'Clínicas de Neurología', 
            'Neurociencias', 
            'Cirugía Ambulatoria', 
            'Centro del Dolor', 
            'Centro Cardíaco Preventivo', 
            'Clínicas Quirúrgicas', 
            'Cirugía para Pérdida de Peso', 
            'Clínicas de Maternidad', 
            'Unidad de Cuidados Intensivos Neonatales', 
            'Servicios Obstétricos y Ginecológicos', 
            'Servicios Pediátricos', 
            'Servicios Quirúrgicos Pediátricos', 
            'Salud Pélvica', 
            'Centro de Diagnóstico Prenatal', 
            'Centro Oncológico', 
            'Centro de Detección de Cáncer', 
            'Programa de Trasplantes', 
            'Psiquiatría'
        ];

        return [
            'nombre' => $this->faker->randomElement($areasPosibles),
        ];
    }
}
