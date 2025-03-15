<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Persona;
use App\Models\User;

class PersonaFactory extends Factory
{
    protected $model = Persona::class;
    
    public function definition()
    {
        return [
            'nombre' => $this->faker->firstName,
            'apellido_paterno' => $this->faker->lastName,
            'apellido_materno' => $this->faker->lastName,
            'sexo' => $this->faker->randomElement(['M', 'F'])
        ];
    }
}
