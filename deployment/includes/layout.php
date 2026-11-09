<?php

declare(strict_types=1);

/** Render the shared page header. All page-specific content follows this. */
function pageStart(string $title): void
{
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= escapeHtml($title) ?> · KCDF Deploy</title>
        <link rel="stylesheet" href="/assets/style.css">
    </head>
    <body>
        <main class="container">
            <section class="panel">
    <?php
}

/** Close the shared page layout. */
function pageEnd(): void
{
    ?>
            </section>
        </main>
    </body>
    </html>
    <?php
}
