<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Ingreso;

class HistorialFactory extends Factory
{
    public function definition()
    {
        return [
            'ingreso_id' => Ingreso::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'fecha_registro' => $this->faker->dateTime(),
            'presion' => $this->faker->randomElement(['120/80', '130/85', '140/90', '110/70']),
            'temperatura' => $this->faker->randomFloat(2, 35, 42),
            'glucosa' => $this->faker->randomFloat(2, 70, 180),
            'sintomatologia' => $this->faker->text(200),
            'observaciones' => $this->faker->optional()->text(100),
        ];
    }
}
