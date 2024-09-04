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
        try {
            // Obtén la información del usuario desde Azure AD
            $azureUser = Socialite::driver('azure')->user();

            // Busca al usuario por su email en tu base de datos
            $user = User::where('name', $azureUser->getName())->first();
            $user->remember_token = \Str::random(60);

            if ($user) {
                \Log::info('User found, logging in.', ['user' => $user]);
            } else {
                // Si el usuario no existe, lo creamos
                $user = User::create([
                    'name' => $azureUser->getName(),
                    'email' => $azureUser->getEmail(),
                    'azure_id' => $azureUser->getId(),
                    'email_verified_at' => now(),
                    'remember_token' => \Str::random(60),
                ]);
            }
            Auth::login($user, true);

            return redirect()->intended('/');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Hubo un problema al iniciar sesión con Azure.');
        }
    }
}
