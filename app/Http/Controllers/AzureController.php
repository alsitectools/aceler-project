<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RootInc\LaravelAzureMiddleware\Azure;

class AzureController extends Controller
{
    /**
     * Redirige al usuario a la página de login de Azure.
     */
    public function login(Request $request)
    {
        $azure = new Azure();
        // dd($azure);
        return $azure->azure($request);
        // dd($azure->azure($request));
    }

    /**
     * Maneja la respuesta de Azure después de la autenticación.
     */
    public function callback(Request $request)
    {
        $azure = new Azure();
        $user = $azure->azurecallback($request);

        if ($user) {
            auth()->login($user);
            return redirect('/');
        }

        return redirect('/login');  // Redirigir si no se puede autenticar al usuario
    }
}
