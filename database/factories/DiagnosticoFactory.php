<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Diagnostico;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Diagnostico>
 */
class DiagnosticoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $diagnosticosPosibles = [
            'Neumonía',
            'Asma',
            'Bronquitis',
            'Diabetes tipo 2',
            'Hipertensión',
            'Infarto de miocardio',
            'Accidente cerebrovascular',
            'Gripe',
            'COVID-19',
            'Hiperlipidemia',
            'Anemia',
            'Apendicitis',
            'Gastritis',
            'Úlcera péptica',
            'Insuficiencia renal',
            'Cálculo biliar',
            'Esguince de tobillo',
            'Fractura de brazo',
            'Artritis',
            'Esclerosis múltiple',
            'Depresión',
            'Ansiedad',
            'Trastorno de pánico',
            'Insomnio',
            'Cáncer de mama',
            'Cáncer de pulmón',
            'Cáncer de próstata',
            'Hepatitis viral',
            'Infección del tracto urinario',
            'Meningitis'
        ];

        return [
            'dx' => $this->faker->randomElement($diagnosticosPosibles), 
            'fecha_diagnostico' => $this->faker->dateTimeBetween('-1 month', 'now'), 
            'estatus' => $this->faker->randomElement(['sospechoso', 'confirmado', 'descartado']),
        ];
    }
}
