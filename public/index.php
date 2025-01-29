<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload_runtime.php';

// Charger les variables d'environnement uniquement en local
if (!isset($_SERVER['APP_ENV']) && file_exists(dirname(__DIR__).'/.env')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// Initialiser Symfony
$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'prod', (bool) ($_SERVER['APP_DEBUG'] ?? false));
$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);