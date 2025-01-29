<?php

use App\Kernel;

use Symfony\Component\Dotenv\Dotenv;

if (!isset($_SERVER['APP_ENV']) && file_exists(dirname(__DIR__).'/.env')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

return function (array $context) {
    return new Kernel($context['APP_ENV'] ?? 'prod', (bool) ($context['APP_DEBUG'] ?? false));
};