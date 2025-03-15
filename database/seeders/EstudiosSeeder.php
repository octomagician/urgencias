<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Estudio;

class EstudiosSeeder extends Seeder
{
    public function run()
    {
        Estudio::factory(100)->create();
    }
}
