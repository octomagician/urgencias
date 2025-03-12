<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Personal;
use App\Models\TiposDeEstudio;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estudios>
 */
class EstudioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'tipos_de_estudios_id' => TiposDeEstudio::inRandomOrder()->first()->id,
            'personal_id' => Personal::inRandomOrder()->first()->id, // Crea un nuevo personal
        ];
    }
}
