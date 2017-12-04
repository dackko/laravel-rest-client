<?php

return [
    'backend' => [
        'url' => env('BACKEND_API_URL'),
        'prefix' => env('BACKEND_API_PREFIX'),
        'endpoints' => [
            'users.index' => [
                // 'url' => 'users',
                // 'fields' => ['roles'],
                // 'parameters' => ['id'],
                // 'query' => ['paginate' => 1, 'perPage' => 16]
            ],
        ]
    ]
];
