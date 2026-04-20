<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InfobipService;

class TestController extends Controller
{
    public function testInfobip(InfobipService $infobip)
    {
        // Intentamos obtener información de la cuenta o simplemente validar la estructura
        return response()->json([
            'status' => 'config_loaded',
            'base_url' => config('services.infobip.base_url'),
            'api_key_last_chars' => substr(config('services.infobip.api_key'), -4)
        ]);
    }
}
