<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserWorkspace;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AzureController extends Controller
{
    public function redirectToAzure()
    {

        return Socialite::driver('azure')->redirect();
    }

    public function handleAzureCallback()
    {
        try {
            // Después de autenticar al usuario y obtener el token
            $azureUser = Socialite::driver('azure')->user();
            $token = $azureUser->token;

            // Crear una instancia del cliente HTTP
            $client = new Client();
            $response = $client->get('https://graph.microsoft.com/v1.0/me?$select=mail,userPrincipalName,displayName,officeLocation,companyName,jobTitle,department,city,country', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Error al obtener los datos del perfil de usuario');
            }

            // Convertir la respuesta JSON a un array PHP
            $userProfile = json_decode($response->getBody()->getContents(), true);

            $mail = $userProfile['mail'] ?? 'No especificado';
            $userPrincipalName = $userProfile['userPrincipalName'] ?? 'No especificado';

            // Busca al usuario por su email o userPrincipalName
            $user = User::where('email', $mail)->orWhere('email', $userPrincipalName)->first();
            Log::info('user devuelto', ['user' => $user]);

            if (!$user) {
                // Crear un nuevo usuario si no existe
                $undefined = 'No especificado';
                $name = $userProfile['displayName'] ?? $undefined;
                $company = $userProfile['companyName'] ?? $undefined;
                $country = $userProfile['country'] ?? $undefined;
                $branch = $userProfile['city'] ?? $undefined;
                $department = $userProfile['department'] ?? $undefined;
                $jobTitle = $userProfile['jobTitle'] ?? $undefined;
                $location = $userProfile['officeLocation'] ?? $undefined;

                switch ($location) {
                    case 'Central Logística' || 'Central':
                        $location = 'Catalunya';
                        break;

                    case 'Norte y Castilla' || 'Olloniego':
                        $location = 'Asturias';
                        break;

                    case 'Levante Sur':
                        $location = 'Alicante';
                        break;

                    case 'Andalucía Occidental':
                        $location = 'Sevilla';
                        break;

                    case 'Andalucía Oriental':
                        $location = 'Málaga';
                        break;

                    case 'Morocco':
                        $location = 'Marruecos';
                        break;

                    default:
                        break;
                }

                $workspace = Workspace::select('id')->where('name', $location)->first();
                Log::info('location ', ['workspaceID' => $workspace->id]);

                // Si el usuario no existe, lo creamos
                $user = User::create([
                    'name' => $name, // "displayName":"Karla Cubias Mejia",
                    'userPrincipalName' => $userPrincipalName, // "userPrincipalName":"kcubias@alsina.com",
                    'email' => $mail, // "mail":"Karla.Cubias@Alsina.com",
                    'company' => $company, // "companyName":"Encofrados J Alsina S.A. Headquarters",
                    'branch' => $branch, // "city":"Montcada i Reixac",
                    'department' => $department, // "department":"CU Producto/Técnico",
                    'country' => $country, // "country":"Spain""mail":"Karla.Cubias@Alsina.com",
                    'jobTitle' => $jobTitle, // "jobTitle":"Headquarters Technician",
                    'officeLocation' => $location, // "officeLocation":"Central", ex. "Perú", "Chile", "México"
                    'type' => $department == 'CU Producto/Técnico' ? 'user' : 'client',
                    'currant_workspace' => $workspace->id, // Integer
                    'email_verified_at' => now(),
                ]);

                $permission = $user->type == 'admin' ? 'Owner' : 'Member';
                UserWorkspace::create([
                    'user_id' => $user->id,
                    'workspace_id' => $user->currant_workspace,
                    'permission' => $permission,
                    'is_active' => 1,
                ]);
            }

            // Iniciar sesión automáticamente con el usuario
            Auth::login($user, true);

            return redirect()->intended('/');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Hubo un problema al iniciar sesión con Azure.');
        }
    }
}
