<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Log;

class LogController extends Controller
{
    public function index()
    {
        $logs = Log::all();
        return response()->json([
            'logs' => $logs
        ], 200);
    }

    public function read($id = null)
    {
        if ($id) {
            $log = Log::find($id);
            if (!$log) {
                return response()->json(['mensaje' => 'No encontrado'], 404);
            }
            return response()->json([
                'logs' => $log
            ], 200);
        } else {
            $Log = Log::all();
            return response()->json([
                'logs' => $Log
            ], 200);
        }
    }

}