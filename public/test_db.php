<?php
$config = require __DIR__ . '/../src/Config.php';
require_once __DIR__ . '/../src/Database.php';

try {
    $db = new Database($config);
    echo "Conexión exitosa a la base de datos.";
} catch (Exception $e) {
    echo "Error de conexión: " . $e->getMessage();
}
