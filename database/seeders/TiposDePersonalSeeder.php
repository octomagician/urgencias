<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TiposDePersonal;

class TiposDePersonalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nombresPosibles = ['Doctor', 'Enfermero', 'Administrativo', 'Técnico', 'Recepcionista'];

        foreach ($nombresPosibles as $nombre) {
            TiposDePersonal::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
