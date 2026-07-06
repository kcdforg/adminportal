<?php

declare(strict_types=1);

/**
 * KCDF Parents API — Web Installer
 * Access: /public/install/ (or /kcdf-api-backend/public/install/ under XAMPP)
 */

session_start();

$root = dirname(__DIR__, 2);
require $root . '/vendor/autoload.php';

use App\Install\Installer;

$installer = new Installer($root);
$basePath  = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/install')), '/');
$apiBase   = dirname($basePath);

if ($installer->isInstalled()) {
    $requestedStep = (int) ($_GET['step'] ?? 0);
    if ($requestedStep !== 4) {
        renderPage('already_installed', [
            'apiBase' => $apiBase,
            'lockFile' => $installer->lockFilePath(),
        ]);
        exit;
    }
    renderPage('wizard', [
        'step' => 4,
        'errors' => [],
        'requirements' => $installer->checkRequirements(),
        'requirementsMet' => true,
        'csrf' => Installer::generateCsrfToken(),
        'basePath' => $basePath,
        'apiBase' => $apiBase,
    ]);
    exit;
}

$step = (int) ($_GET['step'] ?? 1);
$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Installer::validateCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid session. Please refresh and try again.';
    } elseif ($step === 2) {
        $db = [
            'host'     => trim($_POST['db_host'] ?? '127.0.0.1'),
            'port'     => (int) ($_POST['db_port'] ?? 3306),
            'database' => trim($_POST['db_database'] ?? 'kcdf_parents'),
            'username' => trim($_POST['db_username'] ?? 'root'),
            'password' => (string) ($_POST['db_password'] ?? ''),
        ];
        $app = [
            'cors_origins' => trim($_POST['cors_origins'] ?? 'http://localhost:4200,http://localhost:8100'),
        ];

        try {
            if (!$installer->requirementsMet()) {
                throw new RuntimeException('System requirements are not met. Fix the issues on step 1.');
            }
            $installer->testConnection($db);
            $installer->installDatabase($db, $app);
            $_SESSION['installer_db'] = $db;
            header('Location: ' . $basePath . '/?step=3');
            exit;
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
        }
    } elseif ($step === 3) {
        $db = $_SESSION['installer_db'] ?? null;
        if (!is_array($db)) {
            header('Location: ' . $basePath . '/?step=2');
            exit;
        }

        $admin = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'username'   => trim($_POST['username'] ?? ''),
            'email'      => trim($_POST['email'] ?? ''),
            'password'   => (string) ($_POST['password'] ?? ''),
        ];

        if ($admin['first_name'] === '' || $admin['last_name'] === '') {
            $errors[] = 'First name and last name are required.';
        }
        if ($admin['username'] === '') {
            $errors[] = 'Username is required.';
        }
        if ($admin['email'] === '' || !filter_var($admin['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }
        if (strlen($admin['password']) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if ($errors === []) {
            try {
                $installer->createAdminUser($db, $admin);
                $installer->createLockFile();
                unset($_SESSION['installer_db']);
                header('Location: ' . $basePath . '/?step=4');
                exit;
            } catch (Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }
    }
}

$requirements = $installer->checkRequirements();
$csrf = Installer::generateCsrfToken();

renderPage('wizard', [
    'step'          => $step,
    'errors'        => $errors,
    'requirements'  => $requirements,
    'requirementsMet' => $installer->requirementsMet(),
    'csrf'          => $csrf,
    'basePath'      => $basePath,
    'apiBase'       => $apiBase,
]);

// ---------------------------------------------------------------------------
function renderPage(string $template, array $data): void
{
    extract($data);
    require __DIR__ . '/views/' . $template . '.php';
}
