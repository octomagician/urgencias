<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\TiposDeEstudio;

class EstudioFactory extends Factory
{
    public function definition()
    {
        return [
            'tipos_de_estudios_id' => TiposDeEstudio::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
