<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use RootInc\LaravelAzureMiddleware\Azure;

class AzureController extends Controller
{
    public function redirectToAzure()
    {

        return Socialite::driver('azure')->redirect();
    }

    public function handleAzureCallback()
    {
        \Log::info('handleAzureCallback method reached.');

        try {
            // Obtén la información del usuario desde Azure AD
            $azureUser = Socialite::driver('azure')->user();
            \Log::info('Azure OK', ['user' => $azureUser]);

            // Busca al usuario por su email en tu base de datos
            $user = User::where('email', $azureUser->getEmail())->first();

            if ($user) {
                \Log::info('User found, logging in.', ['user' => $user]);

                // Opcionalmente, puedes actualizar información del usuario aquí si es necesario
                $user->name = $azureUser->getName();
                $user->save();
            } else {
                // Si el usuario no existe, lo creamos
                $user = User::create([
                    'name' => $azureUser->getName(),
                    'email' => $azureUser->getEmail(),
                    'azure_id' => $azureUser->getId(),
                    'email_verified_at' => now(), // Marca el correo como verificado
                    'remember_token' => \Str::random(60), // Asigna un remember_token al crear el usuario
                ]);
                \Log::info('User created.', ['user' => $user]);
            }

            // Inicia sesión con el usuario encontrado o recién creado en tu base de datos
            Auth::login($user, true);

            // Redirige al usuario a la página de inicio o la ruta destinada
            return redirect()->intended('/');
        } catch (\Exception $e) {
            \Log::error('Error during callback:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect('/login')->with('error', 'Hubo un problema al iniciar sesión con Azure.');
        }
    }
}
