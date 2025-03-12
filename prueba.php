<?php

require 'vendor/autoload.php';

use MongoDB\Client;

try {
    // Conectar a MongoDB
    $client = new Client("mongodb://127.0.0.1:27017");

    // Seleccionar la base de datos
    $database = $client->selectDatabase('urgencias');

    // Seleccionar la colección
    $collection = $database->selectCollection('logs');

    // Insertar un documento de prueba
    $result = $collection->insertOne([
        'action' => 'test',
        'user_id' => 1,
        'details' => 'Prueba de conexión a MongoDB',
    ]);

    echo "Documento insertado con ID: " . $result->getInsertedId();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}