<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use RootInc\LaravelAzureMiddleware\Azure;

class AzureController extends Controller
{
    public function azure()
    {
        return Socialite::driver('azure')->redirect();
    }

    public function azurecallback()
    {
        try {
            // Obtener la información del usuario autenticado por Azure AD
            $azureUser = Socialite::driver('azure')->user();

            // Encuentra o crea el usuario en tu base de datos
            $authUser = $this->findOrCreateUser($azureUser);

            // Autenticar al usuario en Laravel
            Auth::login($authUser, true);

            // Redirigir al usuario a la página de inicio
            return redirect()->route('home');
        } catch (\Exception $e) {
            // En caso de error, redirigir al login y mostrar un mensaje de error
            return redirect()->route('azure.login')->withErrors('Failed to login with Azure AD');
        }
    }

    public function azurelogout()
    {
        Auth::logout();
        return redirect()->away(config('services.azure.redirect'));
    }

    protected function findOrCreateUser($azureUser)
    {
        // Encuentra o crea un usuario en tu base de datos basado en el correo electrónico de Azure AD
        return User::firstOrCreate(
            ['email' => $azureUser->getEmail()],
            ['name' => $azureUser->getName()]
        );
    }
}
