<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Area;

class AreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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

        foreach ($areasPosibles as $nombre) {
            Area::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
