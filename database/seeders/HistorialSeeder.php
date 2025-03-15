<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Historial;

class HistorialSeeder extends Seeder
{
    public function run()
    {
        Historial::factory(150)->create();
    }
}
