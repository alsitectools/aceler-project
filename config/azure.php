<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tenant ID
    |--------------------------------------------------------------------------
    |
    | This value is equal to the 'Directory (tenant) ID' as found in the Azure
    | portal
    |
    */
    'tenant_id' => env('AZURE_TENANT_ID', '0c589ca1-4ef4-4632-a97f-69301c649eda'),

    /*
    |--------------------------------------------------------------------------
    | Client Info
    |--------------------------------------------------------------------------
    |
    | These values are equal to 'Application (client) ID' and the secret you
    | made in 'Client secrets' as found in the Azure portal
    |
    */
    'client' => [
        'id' => env('AZURE_CLIENT_ID', '130aafe0-2117-499c-8db7-05879d8aa772'),
        'secret' => env('AZURE_CLIENT_SECRET', 'Yd48Q~qcebZrpxFWAOjy3EJyQ5SdR~fxrur6kaxm'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource ID
    |--------------------------------------------------------------------------
    |
    | This value is equal to the 'Object ID' as found in the Azure portal
    |
    */
    'resource' => env('AZURE_RESOURCE', ''),

    /*
    |--------------------------------------------------------------------------
    | Domain Hint
    |--------------------------------------------------------------------------
    |
    | This value can be used to help users know which email address they
    | should login with.
    | https://azure.microsoft.com/en-us/updates/app-service-auth-and-azure-ad-domain-hints/
    |
    */
    'domain_hint' => env('AZURE_DOMAIN_HINT', ''),

    /*
    |--------------------------------------------------------------------------
    | Permission Scope
    |--------------------------------------------------------------------------
    |
    | This value indicates the permissions granted to the OAUTH session.
    | https://docs.microsoft.com/en-us/graph/api/resources/users?view=graph-rest-1.0
    |
    */
    'scope' => env('AZURE_SCOPE', 'User.Read'),
];