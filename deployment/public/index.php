<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/layout.php';

requireLogin();

$status = readDeploymentStatus();
pageStart('Dashboard');
?>
<div class="toolbar">
    <h1>Deployment Manager</h1>

    <form method="post" action="/logout.php">
        <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
        <button type="submit">Log out</button>
    </form>
</div>

<p>
    Select the applications to deploy from the latest <code>main</code> commit.
    Angular and Ionic builds must already be committed to Git.
</p>

<form method="post" action="/deploy.php">
    <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">

    <div class="app">
        <label><input type="checkbox" name="apps[]" value="api"> PHP API</label>
    </div>
    <div class="app">
        <label><input type="checkbox" name="apps[]" value="admin"> Angular Admin (compiled dist)</label>
    </div>
    <div class="app">
        <label><input type="checkbox" name="apps[]" value="member"> Ionic Member (compiled www)</label>
    </div>

    <p><button type="submit" id="deploy-button">Deploy selected applications</button></p>
</form>

<div class="status" aria-live="polite">
    <strong>Deployment status</strong>
    <p id="deployment-state">
        <?= escapeHtml((string) ($status['status'] ?? 'idle')) ?> —
        <?= escapeHtml((string) ($status['step'] ?? '')) ?>
    </p>
    <p class="muted" id="deployment-detail"></p>
</div>

<p class="muted">
    Deployment runs in the background. Refreshing this page does not restart it.
    No deployment history, rollback or database changes are performed.
</p>

<script src="/assets/dashboard.js" defer></script>
<?php pageEnd(); ?>
