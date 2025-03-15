<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Persona; 
use App\Models\TiposDePersonal;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $tipoDePersonal = TiposDePersonal::inRandomOrder()->first();

        return [
            'username' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'persona_id' => Persona::factory(), // Crea una nueva persona o usa una existente
            'tipo_id' => $tipoDePersonal ? $tipoDePersonal->id : TiposDePersonal::factory(), // Usa un tipo existente o crea uno nuevo si no hay
        ];
    }

    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
