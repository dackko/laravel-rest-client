<?php

return [
    'single-service' => true,
    'service-name' => 'backend',

    # API services
    'backend' => [
        'url' => env('BACKEND_API_URL'),
        'prefix' => env('BACKEND_API_PREFIX'),
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
