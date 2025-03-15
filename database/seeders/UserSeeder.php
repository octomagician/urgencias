<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Persona;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        Persona::factory(30)->create()->each(function ($persona) {
            User::factory()->create([
                'persona_id' => $persona->id,
            ])->each(function ($user) {
                $user->assignRole('User');
            });
        });
    }
}