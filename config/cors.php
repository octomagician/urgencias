<?php

return [
    'paths' => ['api/*', 'apiv2/*'], // Asegúrate de incluir 'apiv2/*'
    'allowed_methods' => ['*'], // Permite todos los métodos HTTP
    'allowed_origins' => ['*'], // Permite todos los orígenes (en desarrollo)
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Permite todos los encabezados
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
