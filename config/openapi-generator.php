<?php

return [
    // The URL where the OpenAPI documentation will be accessible
    'api_docs_url' => public_path('api/v1/index.yaml'),

    // The architecture type for the Laravel application
    // (e.g., "default", "laravel_ddd")
    'architecture_type' => 'default',

    // The entities for which code generation will be performed
    'entities' => [
        'route',
        'request',
    ],

    // The directory containing stub files used for code generation
    'stubs' => __DIR__.'/../stubs',

    // Additional paths used in the application
    'paths' => [
        // The file path where the OpenAPI routes will be generated
        'routes_file' => base_path('routes/openapi.php'),
    ],
];
