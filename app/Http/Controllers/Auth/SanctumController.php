<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SanctumController extends Controller
{
    public function login(Request $request){

        $validate = Validator::make(
            $request->all(),
            [
                "email" => "required|email",
                "password" => "required"
            ]
        );

        if($validate->fails()){
            return response()->json(
                [
                    'msg'=>"Datos invalidos",
                    'error'=>$validate->errors()
                ],422);
        }

        //compara una contraseña en texto plano con una contraseña cifrada almacenada
        $user = User::where('email', $request->email)->first();
        if (!$user || ! Hash::check($request->password, $user->password)){
            return response()->json(
                [
                    'msg'=>"Credenciales inválidas"
                ],401);
        }

        return response()->json(
            [
                'msg'=>"Credenciales validadas",
                "token"=>$user->createToken('generic'->plainTextToken)
            ],201);
    }
}
