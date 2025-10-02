<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Aqui você define o guard e broker padrão. Pode continuar com "web"
    | mas nas rotas da empresa/colaborador você vai usar "auth:company"
    | ou "auth:collaborator" explicitamente.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Aqui você cria os guards para cada tipo de autenticação.
    | Empresa e colaborador usam Sanctum.
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'company' => [
            'driver' => 'sanctum',
            'provider' => 'companies',
        ],

        'collaborator' => [
            'driver' => 'sanctum',
            'provider' => 'collaborators',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Cada guard usa um provider, que aponta para o model correto.
    | Empresa -> App\Models\Company
    | Colaborador -> App\Models\Collaborator
    |
    */

    'providers' => [
    
        'companies' => [
            'driver' => 'eloquent',
            'model' => App\Models\Company::class,
        ],

        'collaborators' => [
            'driver' => 'eloquent',
            'model' => App\Models\Collaborator::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Você pode criar brokers diferentes caso queira reset de senha
    | separado para empresa e colaborador.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        'companies' => [
            'provider' => 'companies',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        'collaborators' => [
            'provider' => 'collaborators',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
