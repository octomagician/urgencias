<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'], // Permite todos los métodos HTTP
    'allowed_origins' => ['http://localhost:4200'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Permite todos los encabezados
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
