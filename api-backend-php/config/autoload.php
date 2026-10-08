<?php

declare(strict_types=1);

if (!function_exists('autoloadClass')) {
    function autoloadClass(string $class): void
    {
        static $map = [
            'FastRoute\\' => '/libraries/fastroute-1.3.0/src/',
            'Firebase\\JWT\\' => '/libraries/firebase-php-jwt-7.2.1/src/',
            'Monolog\\' => '/libraries/monolog-2.10.0/src/',
            'Psr\\Log\\' => '/libraries/psr-log-3.0.2/src/',
            'Medoo\\' => '/libraries/medoo-2.6.0/src/',
            'Respect\\Validation\\' => '/libraries/respect-validation-2.2.4/library/',
            'Symfony\\Polyfill\\Mbstring\\' => '/libraries/symfony-polyfill-mbstring-1.33.0/',
        ];

        $classFile = str_replace('\\', '/', $class);
        foreach ($map as $prefix => $libraryPath) {
            $normalizedPrefix = str_replace('\\', '/', $prefix);
            if (strncmp($classFile, $normalizedPrefix, strlen($normalizedPrefix)) !== 0) {
                continue;
            }

            $relativePath = substr($classFile, strlen($normalizedPrefix));
            $path = dirname(__DIR__) . $libraryPath . $relativePath . '.php';
            if (is_file($path)) {
                require_once $path;
                return;
            }
        }
    }
}

spl_autoload_register('autoloadClass');
