<?php

# API services
return [
    'default' => 'backend',

    'backend' => [
        'url' => env('BACKEND_API_URL'),
        'prefix' => env('BACKEND_API_PREFIX'),
        'has-auth' => env('BACKEND_HAS_AUTH', false),
        'login-url' => 'login',
        'home-url' => 'home',

        # Put all the responses here depending on the status code that you receive per service
        'redirects' => [
            'default' => [
                'route' => 'home',
                'message' => null,
                'status' => null
            ],
            // 403 => [
            //     'route' => 'home',
            //     'message' => 'Action is not authorized.'
            // ]
        ],

        'endpoints' => [
            'users.index' => [
                'method' => 'GET'
                // 'url' => 'users',
                // 'fields' => ['roles'],
                // 'parameters' => ['id'],
                // 'query' => ['paginate' => 1, 'perPage' => 16]
            ],
        ]
    ],

    # Put all the responses here depending on the status code that you receive on global scale
    'redirects' => [
        'default' => [
            'route' => 'home',
            'message' => null,
            'status' => null
        ],
        // 403 => [
        //     'route' => 'home',
        //     'message' => 'Action is not authorized.'
        // ]
    ],
];
