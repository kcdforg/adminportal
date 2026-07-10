<?php

declare(strict_types=1);

namespace App\Install;

use PDO;
use PDOException;
use RuntimeException;

class Installer
{
    private string $root;

    public function __construct(?string $root = null)
    {
        $this->root = $root ?? dirname(__DIR__, 2);
    }

    public function lockFilePath(): string
    {
        return $this->root . '/storage/installed.lock';
    }

    public function isInstalled(): bool
    {
        return is_file($this->lockFilePath());
    }

    /** @return array<string, array{label: string, ok: bool, message: string}> */
    public function checkRequirements(): array
    {
        $checks = [];

        $checks['php_version'] = [
            'label'   => 'PHP 8.1 or higher',
            'ok'      => PHP_VERSION_ID >= 80100,
            'message' => 'Current: ' . PHP_VERSION,
        ];

        foreach (['pdo', 'pdo_mysql', 'mbstring', 'json', 'openssl'] as $ext) {
            $checks['ext_' . $ext] = [
                'label'   => "PHP extension: {$ext}",
                'ok'      => extension_loaded($ext),
                'message' => extension_loaded($ext) ? 'Loaded' : 'Missing',
            ];
        }

        $vendorAutoload = $this->root . '/vendor/autoload.php';
        $checks['composer'] = [
            'label'   => 'Composer dependencies (vendor/)',
            'ok'      => is_file($vendorAutoload),
            'message' => is_file($vendorAutoload) ? 'Found' : 'Run: composer install',
        ];

        $schema = $this->root . '/database/schema.sql';
        $checks['schema'] = [
            'label'   => 'Database schema file',
            'ok'      => is_file($schema),
            'message' => is_file($schema) ? 'database/schema.sql' : 'Missing schema.sql',
        ];

        $logsDir = $this->root . '/storage/logs';
        $checks['storage_logs'] = [
            'label'   => 'Writable storage/logs/',
            'ok'      => is_dir($logsDir) && is_writable($logsDir),
            'message' => (is_dir($logsDir) && is_writable($logsDir)) ? 'Writable' : 'Not writable',
        ];

        $storageDir = $this->root . '/storage';
        $checks['storage'] = [
            'label'   => 'Writable storage/ (for .env and lock file)',
            'ok'      => is_dir($storageDir) && is_writable($storageDir),
            'message' => (is_dir($storageDir) && is_writable($storageDir)) ? 'Writable' : 'Not writable',
        ];

        return $checks;
    }

    public function requirementsMet(): bool
    {
        foreach ($this->checkRequirements() as $check) {
            if (!$check['ok']) {
                return false;
            }
        }
        return true;
    }

    /** @param array{host: string, port: int, database: string, username: string, password: string} $db */
    public function testConnection(array $db): void
    {
        $pdo = $this->connectServer($db);
        unset($pdo);
    }

    /**
     * @param array{host: string, port: int, database: string, username: string, password: string} $db
     * @param array{cors_origins: string} $app
     */
    public function installDatabase(array $db, array $app): void
    {
        $pdo = $this->connectServer($db);

        $dbName = $this->escapeIdentifier($db['database']);
        $pdo->exec(
            "CREATE DATABASE IF NOT EXISTS {$dbName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );

        $pdo->exec("USE {$dbName}");

        $schemaPath = $this->root . '/database/schema.sql';
        if (!is_file($schemaPath)) {
            throw new RuntimeException('Schema file not found.');
        }

        $this->executeSqlFile($pdo, $schemaPath);

        $jwtSecret = bin2hex(random_bytes(32));
        $this->writeEnvFile($db, $app, $jwtSecret);
    }

    /**
     * @param array{first_name: string, last_name: string, username: string, email: string, password: string} $admin
     * @param array{host: string, port: int, database: string, username: string, password: string} $db
     */
    public function createAdminUser(array $db, array $admin): void
    {
        $pdo = $this->connectDatabase($db);

        $stmt = $pdo->prepare(
            'INSERT INTO member_profiles (first_name, last_name, email, status, created_at, updated_at)
             VALUES (:first_name, :last_name, :email, :status, NOW(), NOW())'
        );
        $stmt->execute([
            'first_name' => $admin['first_name'],
            'last_name'  => $admin['last_name'],
            'email'      => $admin['email'],
            'status'     => 'active',
        ]);

        $profileId = (int) $pdo->lastInsertId();

        $passwordHash = password_hash($admin['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare(
            'INSERT INTO user_logins (profile_id, username, password_hash, is_active, created_at, updated_at)
             VALUES (:profile_id, :username, :password_hash, 1, NOW(), NOW())'
        );
        $stmt->execute([
            'profile_id'    => $profileId,
            'username'      => $admin['username'],
            'password_hash' => $passwordHash,
        ]);

        $stmt = $pdo->prepare(
            'INSERT INTO admins (profile_id, admin_role, status, created_at, updated_at)
             VALUES (:profile_id, :admin_role, :status, NOW(), NOW())'
        );
        $stmt->execute([
            'profile_id' => $profileId,
            'admin_role' => 'super_admin',
            'status'     => 'active',
        ]);
    }

    public function createLockFile(): void
    {
        $payload = json_encode([
            'installed_at' => date('c'),
            'version'      => '1.0.0',
        ], JSON_PRETTY_PRINT);

        if (file_put_contents($this->lockFilePath(), $payload . PHP_EOL) === false) {
            throw new RuntimeException('Could not write installed.lock file.');
        }
    }

    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['installer_csrf'])) {
            $_SESSION['installer_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['installer_csrf'];
    }

    public static function validateCsrfToken(?string $token): bool
    {
        return is_string($token)
            && !empty($_SESSION['installer_csrf'])
            && hash_equals($_SESSION['installer_csrf'], $token);
    }

    /** @param array{host: string, port: int, database: string, username: string, password: string} $db */
    private function connectServer(array $db): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;charset=utf8mb4',
            $db['host'],
            $db['port']
        );

        try {
            return new PDO($dsn, $db['username'], $db['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    /** @param array{host: string, port: int, database: string, username: string, password: string} $db */
    private function connectDatabase(array $db): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $db['host'],
            $db['port'],
            $db['database']
        );

        try {
            return new PDO($dsn, $db['username'], $db['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    private function executeSqlFile(PDO $pdo, string $path): void
    {
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException('Could not read schema file.');
        }

        $sql = preg_replace('/^--.*$/m', '', $sql) ?? $sql;
        $statements = preg_split('/;\s*\n/', $sql) ?: [];

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ($statement === '') {
                continue;
            }
            $pdo->exec($statement);
        }
    }

    /**
     * @param array{host: string, port: int, database: string, username: string, password: string} $db
     * @param array{cors_origins: string} $app
     */
    private function writeEnvFile(array $db, array $app, string $jwtSecret): void
    {
        $envPath = $this->root . '/.env';
        $examplePath = $this->root . '/.env.example';

        if (!is_file($examplePath)) {
            throw new RuntimeException('.env.example not found.');
        }

        $content = file_get_contents($examplePath);
        if ($content === false) {
            throw new RuntimeException('Could not read .env.example.');
        }

        $replacements = [
            'APP_ENV=development' => 'APP_ENV=production',
            'APP_DEBUG=true'      => 'APP_DEBUG=false',
            'DB_HOST=127.0.0.1'   => 'DB_HOST=' . $db['host'],
            'DB_PORT=3306'        => 'DB_PORT=' . $db['port'],
            'DB_DATABASE=kcdf_parents' => 'DB_DATABASE=' . $db['database'],
            'DB_USERNAME=root'    => 'DB_USERNAME=' . $db['username'],
            'DB_PASSWORD='        => 'DB_PASSWORD=' . $this->escapeEnvValue($db['password']),
            'JWT_SECRET=change-this-to-a-long-random-secret-string' => 'JWT_SECRET=' . $jwtSecret,
            'CORS_ALLOWED_ORIGINS=http://localhost:4200,http://localhost:8100' => 'CORS_ALLOWED_ORIGINS=' . $app['cors_origins'],
        ];

        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        if (file_put_contents($envPath, $content) === false) {
            throw new RuntimeException('Could not write .env file. Check directory permissions.');
        }
    }

    private function escapeEnvValue(string $value): string
    {
        if ($value === '') {
            return '';
        }
        if (preg_match('/[\s#="\']/', $value)) {
            return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $value) . '"';
        }
        return $value;
    }

    private function escapeIdentifier(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }
}
