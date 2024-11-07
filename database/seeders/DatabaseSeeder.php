<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;

use Database\Seeders\UsersSeeder;
use Database\Seeders\PacientesSeeder;
use Database\Seeders\TiposDePersonalSeeder;
use Database\Seeders\PersonalSeeder;
use Database\Seeders\TiposDeEstudiosSeeder;
use Database\Seeders\AreasSeeder;
use Database\Seeders\DiagnosticosSeeder;
use Database\Seeders\IngresosSeeder;
use Database\Seeders\EstudiosSeeder;
use Database\Seeders\HistorialSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //hilo de seeders que quiero que se ejecuten
        $this->call([

            RoleSeeder::class, 
            PacientesSeeder::class,
            
            TiposDePersonalSeeder::class,
            PersonalSeeder::class,
            
            TiposDeEstudiosSeeder::class,
            
            AreasSeeder::class,
            CamasSeeder::class,
            
            DiagnosticosSeeder::class,
            IngresosSeeder::class,
            EstudiosSeeder::class,
            HistorialSeeder::class,
        ]);
    }
}
