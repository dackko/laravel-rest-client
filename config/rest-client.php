<?php

# API services
return [
    'default' => 'backend',

    'backend' => [
        'url' => env('BACKEND_API_URL'),
        'prefix' => env('BACKEND_API_PREFIX'),
        'has-auth' => env('BACKEND_HAS_AUTH', false),
        'method' => env('BACKEND_BEARER_METHOD', 'session'),
        'auth-key' => env('BACKEND_BEARER_KEY', 'token'),
        'endpoints' => [
            'users.index' => [
                'method' => 'GET'
                // 'url' => 'users',
                // 'fields' => ['roles'],
                // 'parameters' => ['id'],
                // 'query' => ['paginate' => 1, 'perPage' => 16]
            ],
        ]
    ]
];
