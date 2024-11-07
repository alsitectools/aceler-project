<?php

namespace App\Providers;

use Laravel\Socialite\Two\AzureProvider;
use Illuminate\Http\Request;

class CustomAzureProvider extends AzureProvider
{
    /**
     * Get the POST URL for the token request.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return 'https://login.microsoftonline.com/' . $this->getTenant() . '/oauth2/v2.0/token';
    }

    /**
     * Overrides the method to send the request as POST.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $options
     * @return \GuzzleHttp\Psr7\Response
     */
    protected function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'form_params' => $this->getTokenFields($code),
            'headers' => ['Accept' => 'application/json'],
        ]);

        return json_decode($response->getBody(), true);
    }
}
