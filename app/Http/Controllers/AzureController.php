<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserTimetable;
use App\Models\UserWorkspace;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AzureController extends Controller
{
    public function redirectToAzure()
    {
        session()->forget(['showModal', 'userProfile']); // Elimina datos de sesión previos

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
            try {
                $photoUrl = 'https://graph.microsoft.com/v1.0/me/photo/$value';
                $photoResponse = $client->get($photoUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                    ],
                ]);

                if ($photoResponse->getStatusCode() === 200) {
                    $photoContent = $photoResponse->getBody()->getContents();
                    $contentType = $photoResponse->getHeaderLine('Content-Type');

                    // Determinar la extensión y guardar la foto
                    $extension = null;
                    if ($contentType === 'image/jpeg') {
                        $extension = 'jpg';
                    } elseif ($contentType === 'image/png') {
                        $extension = 'png';
                    } elseif ($contentType === 'image/gif') {
                        $extension = 'gif';
                    }

                    if ($extension) {
                        $photoPath = 'public/assets/users-avatar/' . $userProfile['userPrincipalName'] . '.' . $extension;
                        $absolutePath = public_path('assets/users-avatar/' . $userProfile['userPrincipalName'] . '.' . $extension);

                        if (!file_exists(dirname($absolutePath))) {
                            mkdir(dirname($absolutePath), 0755, true);
                        }

                        file_put_contents($absolutePath, $photoContent);

                        $userProfile['photo_path'] = 'assets/users-avatar/' . $userProfile['userPrincipalName'] . '.' . $extension;
                    }
                } else {
                    $userProfile['photo_path'] = null;
                }
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // Capturar error 404 (usuario sin foto)
                if ($e->getResponse()->getStatusCode() === 404) {
                    $userProfile['photo_path'] = null;
                } else {
                    throw $e;
                }
            } catch (\Exception $e) {
                $userProfile['photo_path'] = null;
            }

            $mail = $userProfile['mail'] ?? 'No especificado';
            $userPrincipalName = $userProfile['userPrincipalName'] ?? 'No especificado';

            $user = User::where('email', $mail)->orWhere('userPrincipalName', $userPrincipalName)->first();

            if (!$user) {
                session()->forget(['showModal', 'userProfile']); // Limpiar datos previos
                session(['showModal' => true, 'userProfile' => $userProfile]);
                return redirect()->route('login');
            }

            if (isset($userProfile['photo_path'])) {
                $user->avatar = $userProfile['photo_path'];
                $user->save();
            }

            Auth::login($user, true);
            return redirect()->intended('/');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Hubo un problema al iniciar sesión con Azure.');
        }
    }

    public function registerUser(Request $request)
    {
        if (!session()->has('userProfile')) {
            return redirect('/login')->with('error', 'Sesión no válida, intente nuevamente.');
        }
        DB::beginTransaction();
        try {
            $data = $request->all();

            $user = User::updateOrCreate(
                ['email' => $data['mail']],
                [
                    'name' => $data['name'],
                    'userPrincipalName' => $data['userPrincipalName'],
                    'company' => $data['companyName'],
                    'branch' => $data['city'],
                    'department' => $data['department'],
                    'country' => $data['country'],
                    'jobTitle' => $data['jobTitle'],
                    'officeLocation' => $data['officeLocation'],
                    'type' => $data['type'],
                    'currant_workspace' => 1,
                    'lang' => app()->getLocale(),
                    'avatar' => $data['photo_path'],
                    'email_verified_at' => now(),
                    'messenger_color' => '#2180f3',
                    'dark_mode' => 0,
                    'active_status' => 1,
                ]
            );

            $workspaceIds = explode(',', $data['selectedWorkspaceIds']);
            if (!empty($workspaceIds)) {
                $firstWorkspaceId = (int) $workspaceIds[0];

                $workspace = Workspace::find($firstWorkspaceId);
                if ($workspace) {
                    $user->currant_workspace = $firstWorkspaceId;
                    $user->save();
                }

                foreach ($workspaceIds as $workspaceId) {
                    UserWorkspace::create([
                        'user_id' => $user->id,
                        'workspace_id' => (int) $workspaceId,
                        'permission' => 'Member',
                        'is_active' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $workday = json_decode($data['workday'], true);

            UserTimetable::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'monday' => $workday['monday'] ?? null,
                    'tuesday' => $workday['tuesday'] ?? null,
                    'wednesday' => $workday['wednesday'] ?? null,
                    'thursday' => $workday['thursday'] ?? null,
                    'friday' => $workday['friday'] ?? null,
                    'saturday' => $workday['saturday'] ?? null,
                    'sunday' => $workday['sunday'] ?? null,
                    'range_holidays' => null,
                    'range_intensive_workday' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::commit();
            Auth::login($user, true);
            return redirect()->intended('/');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al registrar usuario', 'message' => $e->getMessage()], 500);
        }
    }
}
