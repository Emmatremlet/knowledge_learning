<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

if (file_exists(dirname(__DIR__).'/.env') && $_SERVER['APP_ENV'] !== 'prod') {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};