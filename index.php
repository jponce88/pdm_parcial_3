<?php
require 'vendor/autoload.php';

use Slim\Factory\AppFactory;

// Crear la aplicación
$app = AppFactory::create();

// Incluir las rutas
require __DIR__ . '/includes/conexion_db.php';
require __DIR__ . '/routes/equipos.php';
require __DIR__ . '/routes/jugadores.php';

// Ejecutar la aplicación
$app->run();
