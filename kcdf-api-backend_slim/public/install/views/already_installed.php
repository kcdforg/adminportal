<?php

declare(strict_types=1);

/** @var string $apiBase */
/** @var string $lockFile */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCDF Parents API — Already Installed</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f4f8;
            margin: 0;
            padding: 2rem 1rem;
            color: #1a202c;
        }
        .container { max-width: 640px; margin: 0 auto; }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            padding: 2rem;
        }
        h1 { margin: 0 0 1rem; font-size: 1.5rem; }
        .alert { background: #bee3f8; color: #2a4365; padding: 1rem; border-radius: 6px; margin-bottom: 1rem; }
        code { background: #edf2f7; padding: .15rem .4rem; border-radius: 4px; font-size: .9rem; }
        .btn {
            display: inline-block;
            margin-top: 1rem;
            padding: .65rem 1.25rem;
            background: #2b6cb0;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Already Installed</h1>
        <div class="alert">
            This application has already been installed. The installer is locked.
        </div>
        <p>Lock file: <code><?= htmlspecialchars($lockFile) ?></code></p>
        <p>To reinstall, delete <code>storage/installed.lock</code> and <code>.env</code> manually, then return to the installer.</p>
        <a href="<?= htmlspecialchars($apiBase) ?>/api/v1/auth/login" class="btn">Go to API</a>
    </div>
</div>
</body>
</html>
