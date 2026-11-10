
<?php


/**
 * KCDF Deployment Manager
 * First-run installer helpers.
 *
 * This file handles:
 * - Installation status
 * - Input validation
 * - Administrator configuration
 * - Git deployment configuration
 * - Installation lock creation
 *
 * Important:
 * HestiaCP may restrict PHP from accessing other domains
 * through open_basedir. Therefore, destination directories
 * are validated by path syntax only during installation.
 *
 * Actual filesystem validation is performed by deploy.sh.
 */

const INSTALL_ROOT = __DIR__ . '/..';

const INSTALL_LOCK = INSTALL_ROOT . '/runtime/installed.lock';

const ADMIN_CONFIG = INSTALL_ROOT . '/config/config.php';

const DEPLOY_CONFIG = INSTALL_ROOT . '/config/deploy.env';


/**
 * Return the automatic local Git checkout path.
 *
 * The repository is stored inside the deployment manager,
 * but outside its publicly accessible directory.
 */
function installerRepositoryPath(): string
{
    return dirname(__DIR__) . '/repository';
}


/**
 * Determine whether installation has already occurred.
 *
 * The presence of any configuration file prevents
 * public reinstallation, including after partial setup.
 */
function installerInstalled(): bool
{
    return is_file(INSTALL_LOCK)
        || is_file(ADMIN_CONFIG)
        || is_file(DEPLOY_CONFIG);
}


/**
 * Escape text before displaying it in HTML.
 */
function installerEscape(string $value): string
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


/**
 * Validate the installation form.
 *
 * Destination directories are not accessed from PHP.
 * This avoids HestiaCP open_basedir restrictions.
 *
 * @return array List of validation errors.
 */
function installerValidate(array $input): array
{
    $errors = [];

    // Required installation fields.
    $requiredFields = [
        'username',
        'password',
        'confirm_password',
        'repo_url',
        'branch',
        'api_source',
        'admin_source',
        'member_source',
        'api_dest',
        'admin_dest',
        'member_dest',
    ];

    foreach ($requiredFields as $field) {
        if (
            !is_string($input[$field] ?? null)
            || trim($input[$field]) === ''
        ) {
            $errors[] = "Required field: {$field}";
        }
    }

    if (!empty($errors)) {
        return $errors;
    }

    // Validate administrator username.
    if (
        !preg_match(
            '/^[a-zA-Z0-9_.-]{3,64}$/',
            $input['username']
        )
    ) {
        $errors[] =
            'Username must contain 3–64 valid characters.';
    }

    // Validate password confirmation.
    if (
        !hash_equals(
            $input['password'],
            $input['confirm_password']
        )
    ) {
        $errors[] = 'Passwords do not match.';
    }

    // Validate Git branch name.
    if (
        !preg_match(
            '/^[a-zA-Z0-9._\/-]+$/',
            $input['branch']
        )
        || str_starts_with($input['branch'], '-')
        || str_contains($input['branch'], '..')
        || str_contains($input['branch'], '@{')
        || str_ends_with($input['branch'], '.')
        || str_ends_with($input['branch'], '/')
        || str_contains($input['branch'], '//')
    ) {
        $errors[] = 'Invalid Git branch name.';
    }

    // Accept GitHub SSH or HTTPS repository URLs.
    if (
        !preg_match(
            '~^(git@[^\s]+:[^\s]+|https://[^\s]+)$~',
            $input['repo_url']
        )
    ) {
        $errors[] =
            'Repository URL must use SSH or HTTPS.';
    }

    /*
     * Validate repository-relative source paths.
     *
     * These must not be absolute paths or contain
     * parent-directory traversal.
     */
    $sourceFields = [
        'api_source',
        'admin_source',
        'member_source',
    ];

    foreach ($sourceFields as $field) {
        $path = $input[$field];

        if (
            str_starts_with($path, '/')
            || !preg_match(
                '~^[a-zA-Z0-9_./-]+$~',
                $path
            )
            || in_array('..', explode('/', $path), true)
        ) {
            $errors[] =
                "Invalid repository source path: {$field}";
        }
    }

    /*
     * Validate deployment destinations.
     *
     * Do not use:
     * - is_dir()
     * - realpath()
     * - is_writable()
     *
     * PHP may be restricted from accessing other
     * HestiaCP domains.
     */
    $destinationFields = [
        'api_dest',
        'admin_dest',
        'member_dest',
    ];

    $destinations = [];

    foreach ($destinationFields as $field) {
        $path = rtrim(trim($input[$field]), '/');

        // Require an absolute Unix filesystem path.
        if (!str_starts_with($path, '/')) {
            $errors[] =
                "Destination must be an absolute path: {$field}";

            continue;
        }

        // Reject potentially unsafe path characters.
        if (
            !preg_match(
                '~^/[a-zA-Z0-9_./-]+$~',
                $path
            )
        ) {
            $errors[] =
                "Invalid destination path: {$field}";

            continue;
        }

        // Reject parent-directory traversal.
        if (in_array('..', explode('/', $path), true)) {
            $errors[] =
                "Parent-directory traversal is not allowed: {$field}";

            continue;
        }

        // Restrict destinations to expected HestiaCP paths.
        if (
            !preg_match(
                '~^/home/[^/]+/web/[^/]+/public_html$~',
                $path
            )
        ) {
            $errors[] =
                "Destination must be a HestiaCP public_html path: {$field}";

            continue;
        }

        $destinations[$field] = $path;
    }

    /*
     * Prevent different applications from sharing
     * the same destination.
     */
    if (
        count($destinations)
        !== count(array_unique($destinations))
    ) {
        $errors[] =
            'Application destinations must be different.';
    }

    /*
     * Ensure the private Git checkout does not
     * overlap an application destination.
     */
    $checkout = rtrim(installerRepositoryPath(), '/');

    foreach ($destinations as $destination) {
        if (
            $destination === $checkout
            || str_starts_with(
                $destination . '/',
                $checkout . '/'
            )
            || str_starts_with(
                $checkout . '/',
                $destination . '/'
            )
        ) {
            $errors[] =
                'Git checkout must be separate from deployment destinations.';
        }
    }

    /*
     * Verify that required deployment commands exist.
     *
     * This checks command availability only.
     * Actual destination permissions are checked
     * by the Bash deployment script.
     */
    $requiredCommands = [
        'git',
        'rsync',
        'flock',
        'realpath',
        'nohup',
    ];

    foreach ($requiredCommands as $command) {
        $available =
            is_executable('/usr/bin/' . $command)
            || is_executable('/bin/' . $command);

        if (!$available) {
            $errors[] =
                "Required command not found: {$command}";
        }
    }

    return $errors;
}


/**
 * Safely quote a value for a Bash configuration file.
 *
 * Values are enclosed in single quotes to prevent
 * shell expansion and command substitution.
 */
function installerShellValue(string $value): string
{
    return "'"
        . str_replace("'", "'\\''", $value)
        . "'";
}


/**
 * Create a configuration file exclusively.
 *
 * The file must not already exist.
 */
function installerWriteExclusive(
    string $path,
    string $content
): void {
    $handle = fopen($path, 'x');

    if ($handle === false) {
        throw new RuntimeException(
            'Could not create ' . basename($path)
        );
    }

    chmod($path, 0600);

    try {
        $written = fwrite($handle, $content);

        if (
            $written !== strlen($content)
            || !fflush($handle)
        ) {
            throw new RuntimeException(
                'Could not write ' . basename($path)
            );
        }
    } finally {
        fclose($handle);
    }
}


/**
 * Complete the initial installation.
 *
 * Creates:
 * - Administrator configuration
 * - Deployment configuration
 * - Installation lock file
 *
 * The lock file is created last.
 */
function installerComplete(array $input): void
{
    if (installerInstalled()) {
        throw new RuntimeException(
            'Installation is already configured.'
        );
    }

    /*
     * Administrator credentials.
     *
     * Store only the password hash.
     */
    $adminConfig = [
        'username' => $input['username'],
        'password_hash' => password_hash(
            $input['password'],
            PASSWORD_DEFAULT
        ),
        'session_timeout_seconds' => 1800,
    ];

    $phpContent =
        "<?php\n\n"
        . "declare(strict_types=1);\n\n"
        . "return "
        . var_export($adminConfig, true)
        . ";\n";

    /*
     * Deployment configuration.
     *
     * The local checkout path is calculated
     * automatically instead of being entered
     * through the installer.
     */
    $environment = "# Generated by KCDF installer.\n";
    $environment .= "# Keep this file private.\n\n";

    $configFields = [
        'REPO_URL' => 'repo_url',
        'BRANCH' => 'branch',
        'API_SOURCE' => 'api_source',
        'ADMIN_SOURCE' => 'admin_source',
        'MEMBER_SOURCE' => 'member_source',
        'API_DEST' => 'api_dest',
        'ADMIN_DEST' => 'admin_dest',
        'MEMBER_DEST' => 'member_dest',
    ];

    foreach ($configFields as $key => $field) {
        $environment .=
            $key
            . '='
            . installerShellValue(trim($input[$field]))
            . "\n";
    }

    // Automatically configured Git checkout directory.
    $environment .=
        'REPO_DIR='
        . installerShellValue(installerRepositoryPath())
        . "\n";

    // Keep dry-run enabled for initial verification.
    $environment .= "DRY_RUN=1\n";

    /*
     * Write the configuration files.
     *
     * A partial installation intentionally prevents
     * another public installation attempt.
     */
    installerWriteExclusive(
        ADMIN_CONFIG,
        $phpContent
    );

    installerWriteExclusive(
        DEPLOY_CONFIG,
        $environment
    );

    /*
     * Create installation lock only after both
     * configuration files are successfully written.
     */
    installerWriteExclusive(
        INSTALL_LOCK,
        'Installed at ' . gmdate('c') . "\n"
    );
}
