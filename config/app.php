<?php

return [

    // Application name
    'name' => 'Framework',

    // Debug mode
    'debug' => true,

    // The api prefix
    'api_prefix' => 'api',

    // Bridges
    'bridges' => [
        'general' => [
            App\Http\Bridges\DataTrimmer::class
        ]
    ]
];
