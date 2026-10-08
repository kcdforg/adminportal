<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$config = require __DIR__ . '/config.php';

require_once __DIR__ . '/autoload.php';
require_once $root . '/libraries/fastroute-1.3.0/src/functions.php';
require_once $root . '/services/auth.php';
require_once $root . '/services/identity.php';
require_once $root . '/http/helpers.php';
require_once $root . '/services/logger.php';

validateJwtConfiguration();

if (($config['app']['env'] ?? 'development') === 'production'
    && (empty($config['database']['database']) || empty($config['database']['username']))) {
    throw new RuntimeException('Database configuration is incomplete for production use.');
}

$databaseType = strtolower((string) ($config['database']['type'] ?? 'mysql'));
$pdoOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

if ($databaseType === 'sqlite') {
    $databasePath = (string) ($config['database']['database'] ?? $root . '/storage/app.sqlite');
    $dsn = $databasePath === ':memory:' ? 'sqlite::memory:' : 'sqlite:' . $databasePath;
    $pdo = new PDO($dsn, null, null, $pdoOptions);
} else {
    $dsn = sprintf(
        '%s:host=%s;port=%s;dbname=%s;charset=%s',
        $databaseType,
        $config['database']['host'],
        $config['database']['port'],
        $config['database']['database'],
        $config['database']['charset']
    );
    $pdo = new PDO($dsn, $config['database']['username'], $config['database']['password'], $pdoOptions);
}

$database = new Medoo\Medoo([
    'pdo' => $pdo,
    'type' => $databaseType,
    'database_name' => $config['database']['database'],
    'database' => $config['database']['database'],
    'charset' => $config['database']['charset'],
    'collation' => $config['database']['collation'],
    'logging' => ($config['app']['debug'] ?? false),
]);

return compact('config', 'pdo', 'database');
