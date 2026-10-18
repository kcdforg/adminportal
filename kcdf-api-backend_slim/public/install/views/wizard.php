<?php

declare(strict_types=1);

/** @var int $step */
/** @var array<int, string> $errors */
/** @var array<string, array{label: string, ok: bool, message: string}> $requirements */
/** @var bool $requirementsMet */
/** @var string $csrf */
/** @var string $basePath */
/** @var string $apiBase */

$pageTitles = [
    1 => 'System Requirements',
    2 => 'Database Configuration',
    3 => 'Create Admin Account',
    4 => 'Installation Complete',
];

$title = $pageTitles[$step] ?? 'Installer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCDF Parents API — Installer</title>
    <style>
        * { box-sizing: border-box; }
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
        h1 { margin: 0 0 .25rem; font-size: 1.5rem; }
        .subtitle { color: #718096; margin: 0 0 1.5rem; font-size: .95rem; }
        .steps { display: flex; gap: .5rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .step-pill {
            padding: .35rem .75rem;
            border-radius: 999px;
            font-size: .8rem;
            background: #e2e8f0;
            color: #4a5568;
        }
        .step-pill.active { background: #2b6cb0; color: #fff; }
        .step-pill.done { background: #c6f6d5; color: #22543d; }
        .check-list { list-style: none; padding: 0; margin: 0 0 1.5rem; }
        .check-list li {
            padding: .6rem 0;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .badge-ok { color: #22543d; background: #c6f6d5; padding: .2rem .5rem; border-radius: 4px; font-size: .8rem; }
        .badge-fail { color: #742a2a; background: #fed7d7; padding: .2rem .5rem; border-radius: 4px; font-size: .8rem; }
        label { display: block; font-weight: 600; margin: 1rem 0 .35rem; font-size: .9rem; }
        input[type=text], input[type=password], input[type=email], input[type=number] {
            width: 100%;
            padding: .6rem .75rem;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 1rem;
        }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: .65rem 1.25rem;
            background: #2b6cb0;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover { background: #2c5282; }
        .btn-secondary { background: #718096; }
        .alert {
            padding: .75rem 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: .9rem;
        }
        .alert-error { background: #fed7d7; color: #742a2a; }
        .alert-success { background: #c6f6d5; color: #22543d; }
        .hint { font-size: .85rem; color: #718096; margin-top: .25rem; }
        .complete-icon { font-size: 3rem; text-align: center; margin-bottom: 1rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>KCDF Parents API</h1>
        <p class="subtitle">Web Installer — <?= htmlspecialchars($title) ?></p>

        <div class="steps">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <?php
                $class = 'step-pill';
                if ($i === $step) $class .= ' active';
                elseif ($i < $step) $class .= ' done';
                ?>
                <span class="<?= $class ?>">Step <?= $i ?></span>
            <?php endfor; ?>
        </div>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>

        <?php if ($step === 1): ?>
            <ul class="check-list">
                <?php foreach ($requirements as $check): ?>
                    <li>
                        <span><?= htmlspecialchars($check['label']) ?></span>
                        <span class="<?= $check['ok'] ? 'badge-ok' : 'badge-fail' ?>">
                            <?= $check['ok'] ? 'OK' : htmlspecialchars($check['message']) ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if ($requirementsMet): ?>
                <a href="?step=2" class="btn">Continue to Database Setup</a>
            <?php else: ?>
                <p class="hint">Fix the failed requirements above, then refresh this page.</p>
            <?php endif; ?>

        <?php elseif ($step === 2): ?>
            <form method="post" action="?step=2">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                <label for="db_host">Database Host</label>
                <input type="text" id="db_host" name="db_host" value="127.0.0.1" required>

                <div class="row">
                    <div>
                        <label for="db_port">Port</label>
                        <input type="number" id="db_port" name="db_port" value="3306" required>
                    </div>
                    <div>
                        <label for="db_database">Database Name</label>
                        <input type="text" id="db_database" name="db_database" value="kcdf_parents" required>
                    </div>
                </div>

                <label for="db_username">Database Username</label>
                <input type="text" id="db_username" name="db_username" value="root" required>

                <label for="db_password">Database Password</label>
                <input type="password" id="db_password" name="db_password" value="">
                <p class="hint">XAMPP default is empty.</p>

                <label for="cors_origins">CORS Allowed Origins</label>
                <input type="text" id="cors_origins" name="cors_origins"
                       value="http://localhost:4200,http://localhost:8100">
                <p class="hint">Comma-separated frontend URLs allowed to call the API.</p>

                <button type="submit" class="btn">Install Database &amp; Create .env</button>
            </form>

        <?php elseif ($step === 3): ?>
            <p>Create the first super admin account for the Admin Portal.</p>
            <form method="post" action="?step=3">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                <div class="row">
                    <div>
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div>
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
                <p class="hint">Used to log in to the API and Admin Portal.</p>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="8" required>
                <p class="hint">Minimum 8 characters.</p>

                <button type="submit" class="btn">Create Admin &amp; Finish Installation</button>
            </form>

        <?php elseif ($step === 4): ?>
            <div class="complete-icon">&#10003;</div>
            <div class="alert alert-success">
                Installation completed successfully. The installer is now locked.
            </div>
            <p><strong>Next steps:</strong></p>
            <ul>
                <li>API base URL: <code><?= htmlspecialchars($apiBase) ?>/api/v1</code></li>
                <li>Log in to the Admin Portal with the account you just created.</li>
                <li>Delete or block <code>/public/install/</code> on production servers.</li>
            </ul>
            <a href="<?= htmlspecialchars($apiBase) ?>/api/v1/auth/login" class="btn">Go to API</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
