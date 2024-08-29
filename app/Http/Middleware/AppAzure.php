<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

use RootInc\LaravelAzureMiddleware\Azure as Azure;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;


use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AppAzure extends Azure
{
    protected function success(Request $request, $access_token, $refresh_token, $profile)
    {
        $graph = new Graph();
        $graph->setAccessToken($access_token);

        $graph_user = $graph->createRequest("GET", "/me")
                      ->setReturnType(Model\User::class)
                      ->execute();

        $email = strtolower($graph_user->getUserPrincipalName());

        $user = User::updateOrCreate(['email' => $email], [
            'name' => $graph_user->getGivenName() . ' ' . $graph_user->getSurname(),
        ]);

        Auth::login($user, true);

        return parent::success($request, $access_token, $refresh_token, $profile);
    }
}
