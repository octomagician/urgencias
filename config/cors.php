<?php

return [
    'paths' => ['api/*', 'eventos-sse'], // Asegúrate de incluir 'apiv2/*'
    'allowed_methods' => ['*'], // Permite todos los métodos HTTP
    'allowed_origins' => ['*'], // Permite todos los orígenes (en desarrollo)
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*', 'Authorization', 'Accept', 'Content-Type'], // Permite todos los encabezados
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
