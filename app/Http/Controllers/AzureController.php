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
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AzureController extends Controller
{
    public function redirectToAzure()
    {
        session()->forget(['showModal', 'userProfile']);

        return Socialite::driver('azure')->redirect();
    }

    public function handleAzureCallback()
    {
        try {
            // 1. Obtener token de Azure via Socialite
            try {
                $azureUser = Socialite::driver('azure')->user();
                $token = $azureUser->token;
            } catch (\Exception $e) {
                Log::error('Azure OAuth: Error al obtener token de Socialite', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw new \Exception('Error en autenticación con Azure: ' . $e->getMessage());
            }

            // 2. Consultar Microsoft Graph API
            $client = new Client();
            $graphUrl = 'https://graph.microsoft.com/v1.0/me?$select=mail,userPrincipalName,displayName,officeLocation,companyName,jobTitle,department,city,country';

            try {
                $response = $client->get($graphUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error('Azure OAuth: Error al consultar Microsoft Graph API', [
                    'error' => $e->getMessage(),
                    'token_first_10' => substr($token, 0, 10) . '...',
                ]);
                throw new \Exception('Error al obtener datos del perfil desde Microsoft Graph: ' . $e->getMessage());
            }

            if ($response->getStatusCode() !== 200) {
                Log::error('Azure OAuth: Microsoft Graph API devolvió status no exitoso', [
                    'status' => $response->getStatusCode(),
                    'body' => $response->getBody()->getContents(),
                ]);
                throw new \Exception('Error al obtener los datos del perfil de usuario (status: ' . $response->getStatusCode() . ')');
            }

            $userProfile = json_decode($response->getBody()->getContents(), true);

            if (empty($userProfile) || !is_array($userProfile)) {
                Log::error('Azure OAuth: Respuesta de Graph API vacía o inválida');
                throw new \Exception('Respuesta inválida de Microsoft Graph API');
            }

            // 3. Obtener foto de perfil (opcional — no falla si no tiene)
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

                    $extension = null;
                    if ($contentType === 'image/jpeg') {
                        $extension = 'jpg';
                    } elseif ($contentType === 'image/png') {
                        $extension = 'png';
                    } elseif ($contentType === 'image/gif') {
                        $extension = 'gif';
                    }

                    if ($extension) {
                        $userPrincipalName = $userProfile['userPrincipalName'] ?? 'unknown';
                        $photoPath = 'assets/users-avatar/' . $userPrincipalName . '.' . $extension;
                        $absolutePath = public_path($photoPath);

                        if (!file_exists(dirname($absolutePath))) {
                            mkdir(dirname($absolutePath), 0755, true);
                        }

                        file_put_contents($absolutePath, $photoContent);
                        $userProfile['photo_path'] = asset($photoPath);
                    }
                } else {
                    $userProfile['photo_path'] = null;
                }
            } catch (ClientException $e) {
                if ($e->getResponse() && $e->getResponse()->getStatusCode() === 404) {
                    $userProfile['photo_path'] = null;
                } else {
                    Log::warning('Azure OAuth: Error inesperado al obtener foto de perfil', [
                        'error' => $e->getMessage(),
                    ]);
                    $userProfile['photo_path'] = null;
                }
            } catch (\Exception $e) {
                Log::warning('Azure OAuth: Excepción al obtener foto de perfil', [
                    'error' => $e->getMessage(),
                ]);
                $userProfile['photo_path'] = null;
            }

            // 4. Validar datos mínimos requeridos
            $mail = $userProfile['mail'] ?? null;
            $userPrincipalName = $userProfile['userPrincipalName'] ?? null;

            if (empty($mail) && empty($userPrincipalName)) {
                Log::error('Azure OAuth: mail y userPrincipalName vacíos en respuesta de Graph API', [
                    'userProfile_keys' => array_keys($userProfile),
                ]);
                throw new \Exception('Perfil de Azure sin email ni userPrincipalName');
            }

            // 5. Buscar o crear usuario
            $user = User::where('email', $mail)
                ->orWhere('userPrincipalName', $userPrincipalName)
                ->first();

            if (!$user) {
                Log::info('Azure OAuth: Usuario no encontrado, redirigiendo a registro', [
                    'mail' => $mail,
                    'userPrincipalName' => $userPrincipalName,
                ]);

                session()->forget(['showModal', 'userProfile']);
                session(['showModal' => true, 'userProfile' => $userProfile]);
                return redirect()->route('login');
            }

            // 6. Actualizar avatar si existe foto
            if (isset($userProfile['photo_path'])) {
                $user->avatar = $userProfile['photo_path'];
                $user->save();
            }

            Auth::login($user, true);

            Log::info('Azure OAuth: Login exitoso', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->intended('/');

        } catch (\Exception $e) {
            Log::error('Azure OAuth: Fallo general en handleAzureCallback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/login')->with('error', 'Hubo un problema al iniciar sesión con Azure.');
        }
    }

    public function registerUser(Request $request)
    {
        if (!session()->has('userProfile')) {
            Log::warning('Azure OAuth: Intento de registro sin userProfile en sesión');
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
                    'avatar' => null,
                    'email_verified_at' => now(),
                    'messenger_color' => '#2180f3',
                    'dark_mode' => 0,
                    'active_status' => 1,
                    'number_employee' => $data['employee_number'],
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

            if (!empty($data['workday'])) {
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
            }

            if (!empty($data['photo_path'])) {
                $ext = pathinfo(parse_url($data['photo_path'], PHP_URL_PATH), PATHINFO_EXTENSION);
                $old = public_path('assets/users-avatar/' . $data['userPrincipalName'] . '.' . $ext);
                $new = 'assets/users-avatar/' . $user->id . '.' . $ext;
                if (file_exists($old)) {
                    rename($old, public_path($new));
                    $user->avatar = asset($new);
                    $user->save();
                }
            }


            DB::commit();
            Auth::login($user, true);

            Log::info('Azure OAuth: Usuario registrado y logueado exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->intended('/');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Azure OAuth: Error en registerUser', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'token', 'secret']), // evitar loggear datos sensibles
            ]);

            return response()->json(['error' => 'Error al registrar usuario', 'message' => $e->getMessage()], 500);
        }
    }
}
