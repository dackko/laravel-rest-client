<?php

return [
    'backend' => [
        'url' => env('STUDIO_API_URL'),
        'prefix' => env('STUDIO_API_PREFIX'),
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
