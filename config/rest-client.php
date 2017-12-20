<?php

# API services
return [
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
