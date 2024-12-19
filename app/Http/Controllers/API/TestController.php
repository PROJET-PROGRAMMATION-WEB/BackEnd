<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {
        return response()->json([
            'message' => 'API fonctionne correctement!',
            'status' => 'success'
        ]);
    }

    public function hello($name = 'Invité')
    {
        return response()->json([
            'message' => "Bonjour $name !",
            'time' => now()->format('H:i:s')
        ]);
    }

    public function info()
    {
        return response()->json([
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_time' => now(),
            'environment' => app()->environment()
        ]);
    }
}
