<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class practicaunoController extends Controller
{
    public function tabla(int $tab1 = 0, int $tab2 = 0, string $fibo = null)
    {
        $tablas = []; // Almacenar resultados

        if ($fibo === null) { // Casos 1 y 2
            if ($tab2 == 0) { // Caso con 1 parámetro
                $tablas[] = [
                    "*1" => $tab1 * 1,
                    "*2" => $tab1 * 2,
                    "*3" => $tab1 * 3,
                    "*4" => $tab1 * 4,
                    "*5" => $tab1 * 5,
                    "*6" => $tab1 * 6,
                    "*7" => $tab1 * 7,
                    "*8" => $tab1 * 8,
                    "*9" => $tab1 * 9,
                    "*10" => $tab1 * 10,
                ];
            } else if ($tab1 > $tab2 && ($tab2 != 0)) { // Caso imposible
                $tablas[] = ["imposible"];
            } else if ($tab2 != 0) { // Caso con dos parámetros
                for ($i = 0; $i <= ($tab2 - $tab1); $i++) { // Corregido aquí
                    $tablas[] = [
                        "*1" => ($tab1 + $i) * 1,
                        "*2" => ($tab1 + $i) * 2,
                        "*3" => ($tab1 + $i) * 3,
                        "*4" => ($tab1 + $i) * 4,
                        "*5" => ($tab1 + $i) * 5,
                        "*6" => ($tab1 + $i) * 6,
                        "*7" => ($tab1 + $i) * 7,
                        "*8" => ($tab1 + $i) * 8,
                        "*9" => ($tab1 + $i) * 9,
                        "*10" => ($tab1 + $i) * 10,
                    ];
                }
            }
            
        }
         else if 
             { // Caso 3
                if ($fibo == "fibonacci")
                    $fibonacci = [0, 1];
                    for ($i = 2; $i <= $tab2; $i++) {
                        $fibonacci[] = $fibonacci[$i - 1] + $fibonacci[$i - 2];
                    }
                    foreach ($fibonacci as $num) {
                        if ($num >= $tab1 && $num <= $tab2) {
                            $tablas[] = $num;
                        }
                    }
                else
                {
                    $fibo = "palabra incorrecta";
                    $tab1 = 0
                    $tab2= 0
                }
            }

        // Respuesta final
        return response()->json([
            "primero" => $tab1,
            "segundo" => $tab2,
            "palabra" => $fibo,
            "resultado" => $tablas
        ], 200);
    }
}
