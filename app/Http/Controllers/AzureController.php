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
use Illuminate\Support\Facades\Storage;


class AzureController extends Controller
{
    public function redirectToAzure()
    {
        return Socialite::driver('azure')->redirect();
    }
    public function handleAzureCallback()
    {
        try {
            $azureUser = Socialite::driver('azure')->user();
            $token = $azureUser->token;

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

            $userProfile = json_decode($response->getBody()->getContents(), true);

            // Intentar obtener la foto de perfil del usuario
            $photoUrl = 'https://graph.microsoft.com/v1.0/me/photo/$value';

            try {
                $photoResponse = $client->get($photoUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                    ],
                ]);

                if ($photoResponse->getStatusCode() === 200) {
                    $contentType = $photoResponse->getHeaderLine('Content-Type');

                    if (in_array($contentType, ['image/jpeg', 'image/png', 'image/gif'])) {

                        $photoContent = $photoResponse->getBody()->getContents();
                        $photoPath = 'users-avatar/' . $userProfile['userPrincipalName'] . '.jpg';
                        Storage::disk('public')->put($photoPath, $photoContent);
                        $userProfile['photo_path'] = $photoPath;
                    } else {

                        $userProfile['photo_path'] = null;
                    }
                } else {

                    $userProfile['photo_path'] = null;
                }
            } catch (\Exception $e) {

                $userProfile['photo_path'] = null;
            }

            $user = $this->findOrcreate($userProfile);
            Auth::login($user, true);

            return redirect()->intended('/');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Hubo un problema al iniciar sesión con Azure.');
        }
    }


    public function findOrcreate($userProfile)
    {
        $undefined = 'No especificado';

        $mail = $userProfile['mail'] ?? $undefined;
        $userPrincipalName = $userProfile['userPrincipalName'] ?? $undefined;

        // Busca al usuario por su email o userPrincipalName
        $user = User::where('email', $mail)->orWhere('email', $userPrincipalName)->first();

        if (!$user) {
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
                case 'Houston':
                    $location = 'Texas';
                    break;
                case 'Miami':
                    $location = 'Florida';
                    break;

                default:

                    break;
            }

            // Si es la central
            if (isset($department) && str_starts_with($department, 'BU')) {

                if (strpos($department, '/') === false) {
                    $workspaceName = explode(' ', $department);
                    $name = $workspaceName[1];
                    $type = 'user';
                } else {
                    $arrayDepartment = explode('/', $department);
                    $workspaceName = explode(' ', $arrayDepartment[0]);
                    $name = $workspaceName[1];
                    $type = in_array($arrayDepartment[1], ['Técnico', 'Sistemas', 'I+D']) ? 'user' : 'client';
                }

                $workspace = Workspace::select('id')->where('name', $name)->first();
            } else {
                $arrayDepartment = explode('/', $department);

                $type = in_array($arrayDepartment[1], ['Técnico', 'Sistemas', 'I+D']) ? 'user' : 'client';
                $workspace = Workspace::select('id')->where('name', $location)->first();
            }

            // Si el usuario no existe, lo creamos
            $user = User::create([
                'name' => $name,
                'userPrincipalName' => $userPrincipalName,
                'email' => $mail,
                'company' => $company,
                'branch' => $branch,
                'department' => $department,
                'country' => $country,
                'jobTitle' => $jobTitle,
                'officeLocation' => $location,
                'type' => $type,
                'currant_workspace' => $workspace ? $workspace->id : '',
                'email_verified_at' => now(),
                'avatar' => $userProfile['photo_path'] ?? null,
            ]);

            UserWorkspace::create([
                'user_id' => $user->id,
                'workspace_id' => $user->currant_workspace,
                'permission' => 'Member',
                'is_active' => 1,
            ]);
        }

        return $user;
    }
}
