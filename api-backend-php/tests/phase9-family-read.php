<?php

declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/config/autoload.php';
require $root . '/services/identity.php';

if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
    fwrite(STDERR, "phase-9 checks require PDO_SQLITE\n");
    exit(2);
}

$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$database = new Medoo\Medoo(['pdo' => $pdo, 'type' => 'sqlite', 'database' => ':memory:']);
$pdo->exec('CREATE TABLE addresses (id INTEGER PRIMARY KEY, address_line_1 TEXT NOT NULL)');
$pdo->exec('CREATE TABLE families (id INTEGER PRIMARY KEY, family_code TEXT NOT NULL, family_name TEXT NOT NULL, address_id INTEGER NULL)');
$pdo->exec("INSERT INTO addresses (id, address_line_1) VALUES (4, 'Main Street')");
$pdo->exec("INSERT INTO families (id, family_code, family_name, address_id) VALUES (1, 'FAM-001', 'Example Family', 4)");

$family = getFamily($database, 1);
if ($family === null
    || $family['id'] !== 1
    || $family['family_code'] !== 'FAM-001'
    || $family['address']['id'] !== 4) {
    throw new RuntimeException('The family detail query must return the family and its address.');
}

echo "Phase 9 family read check passed.\n";
