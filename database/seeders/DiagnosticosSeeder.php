<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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

class DiagnosticosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Diagnostico::factory(30)->create();
    }
}
