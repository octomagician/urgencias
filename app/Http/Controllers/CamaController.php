<?php

namespace App\Http\Controllers;

use App\Models\Cama;
use App\Models\Area;
use Illuminate\Http\Request;
use Faker\Factory as Faker;
use App\Models\Token;
use Illuminate\Support\Facades\Http;

//humanos
class CamaController extends Controller
{
        public function create(Request $request)
        {
            $request->validate([
                'numero_cama' => 'required|integer|unique:camas,numero_cama',
                'area_id' => 'required|exists:areas,id'
            ]);
    
            $cama = Cama::create([
                'numero_cama' => $request->numero_cama,
                'area_id' => $request->area_id
            ]);
    
            return response()->json($cama, 201);
        }
    
        public function read($id = null)
        {
            if ($id) {
                $cama = Cama::find($id);
                if (!$cama) {
                    return response()->json(['message' => 'No encontrado'], 404);
                }
            } else {
                $cama = Cama::all();
            }
        
            return response()->json($cama, 200);
        }
        
        public function update(Request $request, $id)
        {
            $cama = Cama::find($id);
            if (!$cama) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
    
            $request->validate([
                'numero_cama' => 'required|integer|unique:camas,numero_cama,' . $cama->id,
                'area_id' => 'required|exists:areas,id'
            ]);
    
            $cama->update($request->only(['numero_cama', 'area_id']));
            return response()->json($cama);
        }
    
        public function delete($id)
        {
            $cama = Cama::find($id);
            if (!$cama) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
    
            $cama->delete();
            return response()->json(['message' => 'Eliminado'], 204);
        }
    }