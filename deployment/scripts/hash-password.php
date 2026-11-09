<?php

declare(strict_types=1);

/** Generate a password hash without storing the password in shell history. */
if (PHP_SAPI !== 'cli') {
    exit(1);
}

fwrite(STDOUT, 'Enter new deployment password: ');

// Hide password input when running in a terminal (Unix/Linux).
$hasTerminal = function_exists('shell_exec') && function_exists('posix_isatty')
    && posix_isatty(STDIN);

if ($hasTerminal) {
    shell_exec('stty -echo');
}

try {
    $password = rtrim((string) fgets(STDIN), "\r\n");
} finally {
    if ($hasTerminal) {
        shell_exec('stty echo');
    }
    fwrite(STDOUT, "\n");
}

if (strlen($password) < 14) {
    fwrite(STDERR, "Use a password of at least 14 characters.\n");
    exit(1);
}

echo password_hash($password, PASSWORD_DEFAULT), PHP_EOL;
