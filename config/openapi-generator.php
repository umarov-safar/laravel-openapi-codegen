<?php

return [

    'path' => public_path('api/v1/index.yaml'),

    // laravel_ddd domain driven develop
    'architecture_type' => 'default',

    'route_path' => app_path('route/openapi-routes.php'),

    'entities' => [
        'route',
    ],


    'stubs' => __DIR__ . '/../stubs'
];
