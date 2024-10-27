<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function login(Request $request)
    {
    try { 
        $validate = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]
        );

        if ($validate->fails()) {
            return response()->json(
                [
                    'msg' => 'Datos inválidos',
                    'error' => $validate->errors()
                ], 422
            );
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    'msg' => 'Credenciales inválidas'
                ], 401
            );
        }

        //generanding token y aparte trycatch
        try {
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json(
                    ['msg' => 'Error al crear el token'],
                    500
                );
            }
        } catch (JWTException $e) {
            return response()->json(
                ['msg' => 'No se pudo crear el token'],
                500
            );
        }

        return response()->json(
            [
                'msg' => 'Credenciales validadas',
                'token' => $token
            ], 201
        );
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 422);
    }  
    }
}
