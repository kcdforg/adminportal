# Phase 8 — Swagger/OpenAPI API Documentation

## Context

You are building API documentation for the `kcdf-api-backend` — a Slim Framework 4 REST API backend for the KCDF Parents platform.

The backend is complete with all 6 modules implemented:
1. Auth
2. Families
3. Academics
4. Payments
5. Community
6. Notifications

This phase covers:
1. Installing and configuring swagger-php library
2. Setting up OpenAPI specification generation
3. Adding Swagger annotations to all controller methods
4. Serving interactive Swagger UI documentation at `/swagger`
5. Auto-generating swagger.json on demand

Do NOT modify any existing business logic or routes. Only add documentation annotations and setup files.

---

## Tech Stack

- **swagger-php** (^4.0) — OpenAPI annotation library for PHP
- **Swagger UI** (bundled) — Interactive API documentation frontend
- **OpenAPI 3.0** specification — Industry-standard API documentation format
- Existing: Slim Framework 4, PHP 8.2+

---

## Objectives

1. **Install swagger-php package** via Composer
2. **Create OpenAPI configuration** (`config/openapi.php`)
3. **Implement script to generate swagger.json** (`scripts/generate-swagger.php`)
4. **Create separate documentation routes file** (`routes/documentation.php`) — OPTION 1
5. **Update public/index.php** to load documentation routes
6. **Create Swagger UI HTML page** (`public/swagger/swagger-ui.html`)
7. **Create Documentation controller** (`src/Modules/Documentation/Controllers/DocumentationController.php`)
8. **Annotate all controllers** with OpenAPI documentation
9. **Document all DTOs/Models** for request/response schemas
10. **Update composer.json scripts** to auto-generate docs

---

## Project Structure Changes

```
kcdf-api-backend/
├── composer.json                    ← add zircote/swagger-php, update scripts
├── config/
│   └── openapi.php                  ← NEW: OpenAPI metadata & settings
├── scripts/
│   └── generate-swagger.php         ← NEW: Generate swagger.json
├── routes/
│   ├── api.php                      ← existing (no changes)
│   └── documentation.php            ← NEW: Swagger routes (OPTION 1)
├── public/
│   ├── index.php                    ← UPDATE: load documentation routes
│   └── swagger/                     ← NEW: Swagger UI files
│       ├── swagger-ui.html          ← NEW: UI page
│       └── swagger.json             ← AUTO-GENERATED: OpenAPI spec
├── src/
│   └── Modules/
│       ├── Documentation/           ← NEW: Documentation module
│       │   └── Controllers/
│       │       └── DocumentationController.php
│       ├── Auth/
│       │   ├── Controllers/
│       │   │   └── AuthController.php          ← ADD annotations
│       │   ├── DTOs/
│       │   │   ├── LoginRequestDTO.php         ← ADD @OA\Schema
│       │   │   └── AuthResponseDTO.php         ← ADD @OA\Schema
│       │   └── routes.php                      ← no changes
│       ├── Families/
│       │   ├── Controllers/                    ← ADD annotations to all
│       │   │   ├── MemberProfileController.php
│       │   │   ├── FamilyController.php
│       │   │   ├── AddressController.php
│       │   │   ├── TrainerController.php
│       │   │   ├── AdminController.php
│       │   │   └── EntityController.php
│       │   └── DTOs/                           ← ADD @OA\Schema to all
│       ├── Academics/
│       │   ├── Controllers/                    ← ADD annotations to all
│       │   └── DTOs/                           ← ADD @OA\Schema to all
│       ├── Payments/
│       │   ├── Controllers/                    ← ADD annotations to all
│       │   └── DTOs/                           ← ADD @OA\Schema to all
│       ├── Community/
│       │   ├── Controllers/                    ← ADD annotations to all
│       │   └── DTOs/                           ← ADD @OA\Schema to all
│       └── Notifications/
│           ├── Controllers/                    ← ADD annotations to all
│           └── DTOs/                           ← ADD @OA\Schema to all
└── .gitignore                       ← add public/swagger/swagger.json
```

---

## Implementation Steps

### Step 1: Install swagger-php Package

Run the following command:

```bash
cd kcdf-api-backend
composer require --dev zircote/swagger-php
```

Verify installation:

```bash
vendor/bin/openapi --version
```

Update `composer.json` scripts section to include:

```json
"scripts": {
    "start": "php -S localhost:8080 -t public",
    "docs": "php scripts/generate-swagger.php",
    "test": "phpunit"
}
```

---

### Step 2: Create OpenAPI Configuration

Create **`config/openapi.php`**:

```php
<?php

declare(strict_types=1);

return [
    'title' => 'KCDF Parents Platform — API',
    'description' => 'REST API for the KCDF Parents platform. Serves Parent App and Admin Portal.',
    'version' => '1.0.0',
    'servers' => [
        'development' => 'http://localhost:8080',
        'production' => 'https://api.example.com',
    ],
    'contact' => [
        'name' => 'KCDF Support',
        'email' => 'support@kcdf.org',
    ],
    'license' => [
        'name' => 'Proprietary',
    ],
    'externalDocs' => [
        'description' => 'Full API Documentation',
        'url' => 'https://docs.kcdf.org/api',
    ],
];
```

---

### Step 3: Create Swagger Generation Script

Create **`scripts/generate-swagger.php`**:

```php
<?php

declare(strict_types=1);

$rootDir = dirname(__DIR__);
$config = require $rootDir . '/config/openapi.php';

use OpenAPI\Generator;

// Scan the src directory for OpenAPI annotations
$openapi = Generator::scan(
    [$rootDir . '/src/Modules'],
    [
        'exclude' => [$rootDir . '/src/Middleware'],
    ]
);

// Set the OpenAPI version and base info
$openapi->openapi = '3.0.0';
$openapi->info->title = $config['title'];
$openapi->info->description = $config['description'];
$openapi->info->version = $config['version'];

// Add contact info
$openapi->info->contact = new \OpenAPI\Annotations\Contact([
    'name' => $config['contact']['name'],
    'email' => $config['contact']['email'],
]);

// Add license
$openapi->info->license = new \OpenAPI\Annotations\License([
    'name' => $config['license']['name'],
]);

// Add servers
$openapi->servers = [
    new \OpenAPI\Annotations\Server([
        'url' => $config['servers']['development'],
        'description' => 'Development server',
    ]),
    new \OpenAPI\Annotations\Server([
        'url' => $config['servers']['production'],
        'description' => 'Production server',
    ]),
];

// Add security schemes
$openapi->components = new \OpenAPI\Annotations\Components([
    'securitySchemes' => [
        'bearerAuth' => new \OpenAPI\Annotations\SecurityScheme([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
            'description' => 'JWT Bearer token for authentication',
        ]),
    ],
]);

// Create output directory
$outputDir = $rootDir . '/public/swagger';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$outputFile = $outputDir . '/swagger.json';
file_put_contents($outputFile, json_encode($openapi, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

echo "✓ Swagger JSON generated at: {$outputFile}\n";
echo "  Spec: OpenAPI " . $openapi->openapi . "\n";
echo "  Version: " . $openapi->info->version . "\n";
```

---

### Step 4: Create Separate Documentation Routes File

Create **`routes/documentation.php`** (NEW FILE — OPTION 1):

This keeps Swagger documentation routes separate from API routes, providing clear architectural separation.

```php
<?php

declare(strict_types=1);

use Slim\App;

return function (App $app) {
    
    // Swagger UI HTML page
    $app->get('/swagger', 'App\Modules\Documentation\Controllers\DocumentationController:swagger')
        ->setName('swagger.ui');
    
    // Swagger OpenAPI JSON specification
    $app->get('/swagger/swagger.json', function ($request, $response) {
        $filePath = __DIR__ . '/../public/swagger/swagger.json';
        
        if (!file_exists($filePath)) {
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => 'Swagger documentation not generated. Run: composer docs']));
        }
        
        $json = file_get_contents($filePath);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write($json);
    });
};
```

---

### Step 5: Update public/index.php

Update **`public/index.php`** to load both API and documentation routes:

```php
<?php

declare(strict_types=1);

// TEMPORARY: Enable error display for debugging
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$root = dirname(__DIR__);
$lockFile = $root . '/storage/installed.lock';

if (!is_file($lockFile)) {
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    header('Location: ' . ($base === '' ? '' : $base) . '/install/');
    exit;
}

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable($root);
$dotenv->load();

// TEMPORARY: Force debug mode for troubleshooting
putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';

// Build DI container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/container.php');
$container = $containerBuilder->build();

// Create Slim app
AppFactory::setContainer($container);
$app = AppFactory::create();

// Load bootstrap (middleware, error handling)
(require __DIR__ . '/../bootstrap/app.php')($app);

// Load API routes (under /api/v1)
(require __DIR__ . '/../routes/api.php')($app);

// Load documentation routes (Swagger under /swagger)
(require __DIR__ . '/../routes/documentation.php')($app);

$app->run();
```

---

### Step 6: Create Swagger UI HTML Page

Create **`public/swagger/swagger-ui.html`**:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCDF Parents Platform — API Documentation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@4/swagger-ui.css">
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }
        *,
        *:before,
        *:after {
            box-sizing: inherit;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
        }
        .topbar {
            background-color: #fafafa;
            padding: 10px 0;
            border-bottom: 1px solid #cccccc;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>KCDF Parents Platform — API Documentation</h1>
    </div>
    <div id="swagger-ui"></div>

    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@4/swagger-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@4/swagger-ui-bundle.js"></script>
    <script>
        window.onload = function() {
            SwaggerUIBundle({
                url: '/swagger/swagger.json',
                dom_id: '#swagger-ui',
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIBundle.SwaggerUIStandalonePreset,
                ],
                layout: 'BaseLayout',
                deepLinking: true,
                showExtensions: true,
                defaultModelsExpandDepth: 1,
            });
        };
    </script>
</body>
</html>
```

---

### Step 7: Create Documentation Controller

Create **`src/Modules/Documentation/Controllers/DocumentationController.php`** (NEW MODULE):

```php
<?php

declare(strict_types=1);

namespace App\Modules\Documentation\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DocumentationController
{
    public function swagger(Request $request, Response $response): Response
    {
        $filePath = __DIR__ . '/../../../public/swagger/swagger-ui.html';
        
        if (!file_exists($filePath)) {
            $response->getBody()->write(json_encode([
                'error' => 'Documentation not available. Run: composer docs',
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(503);
        }

        $html = file_get_contents($filePath);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }
}
```

---

### Step 8: Annotate Controller Methods

For **every controller method**, add OpenAPI annotations. Example pattern for `AuthController.php`:

```php
<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Core\BaseController;
use App\Modules\Auth\Services\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController extends BaseController
{
    public function __construct(private readonly AuthService $authService) {}

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     operationId="authLogin",
     *     tags={"Auth"},
     *     summary="User login",
     *     description="Authenticate user with username and password. Returns access and refresh tokens.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Login credentials",
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function login(Request $request, Response $response): Response
    {
        // ... implementation (no changes)
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/refresh",
     *     operationId="authRefresh",
     *     tags={"Auth"},
     *     summary="Refresh access token",
     *     description="Issue new access and refresh tokens using a valid refresh token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RefreshTokenRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid refresh token"
     *     )
     * )
     */
    public function refresh(Request $request, Response $response): Response
    {
        // ... implementation (no changes)
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     operationId="authLogout",
     *     tags={"Auth"},
     *     summary="User logout",
     *     description="Invalidate the user's refresh token and logout.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LogoutRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function logout(Request $request, Response $response): Response
    {
        // ... implementation (no changes)
    }

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     operationId="getCurrentProfile",
     *     tags={"Auth"},
     *     summary="Get current user profile",
     *     description="Retrieve the authenticated user's profile information.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved",
     *         @OA\JsonContent(ref="#/components/schemas/MemberProfileResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Profile not found"
     *     )
     * )
     */
    public function me(Request $request, Response $response): Response
    {
        // ... implementation (no changes)
    }
}
```

---

### Step 9: Annotate DTO Classes

For every DTO and Model, add OpenAPI schema annotations.

Example for `LoginRequestDTO.php`:

```php
<?php

declare(strict_types=1);

namespace App\Modules\Auth\DTOs;

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"username", "password"},
 *     title="Login Request",
 *     description="Credentials for user authentication",
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="User username",
 *         example="john.doe",
 *         minLength=1
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         description="User password",
 *         example="secret123",
 *         minLength=1
 *     )
 * )
 */
class LoginRequestDTO
{
    public function __construct(
        public string $username,
        public string $password,
    ) {}
}
```

Example for `AuthResponseDTO.php`:

```php
<?php

declare(strict_types=1);

namespace App\Modules\Auth\DTOs;

/**
 * @OA\Schema(
 *     schema="AuthResponse",
 *     type="object",
 *     title="Authentication Response",
 *     description="Successful authentication response with tokens and profile",
 *     @OA\Property(
 *         property="access_token",
 *         type="string",
 *         description="JWT access token",
 *         example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
 *     ),
 *     @OA\Property(
 *         property="refresh_token",
 *         type="string",
 *         description="JWT refresh token",
 *         example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
 *     ),
 *     @OA\Property(
 *         property="profile",
 *         ref="#/components/schemas/MemberProfileResponse"
 *     )
 * )
 */
class AuthResponseDTO
{
    public function __construct(
        public string $access_token,
        public string $refresh_token,
        public array $profile,
    ) {}
}
```

---

### Step 10: Define Common Response Schemas

Add common response schemas to **`src/Core/BaseController.php`** (before the class definition):

```php
<?php

/**
 * @OA\Info(
 *     title="KCDF Parents Platform API",
 *     version="1.0.0",
 *     description="REST API for the KCDF Parents platform",
 *     contact={"name": "KCDF Support", "email": "support@kcdf.org"},
 *     license={"name": "Proprietary"}
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="Development Server"
 * )
 *
 * @OA\Server(
 *     url="https://api.example.com",
 *     description="Production Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     name="bearerAuth",
 *     in="header",
 *     bearerFormat="JWT",
 *     scheme="bearer",
 *     description="JWT Bearer token authentication"
 * )
 *
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     title="Success Response",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="data", type="object"),
 *     @OA\Property(property="message", type="string", example="Operation successful")
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="Error Response",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(
 *         property="error",
 *         type="object",
 *         @OA\Property(property="code", type="string", example="VALIDATION_FAILED"),
 *         @OA\Property(property="message", type="string", example="Request validation failed"),
 *         @OA\Property(property="details", type="object")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     title="Validation Error Response",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(
 *         property="error",
 *         type="object",
 *         @OA\Property(property="code", type="string", example="VALIDATION_FAILED"),
 *         @OA\Property(property="message", type="string", example="Validation failed"),
 *         @OA\Property(
 *             property="details",
 *             type="object",
 *             example={"username": {"The username field is required."}, "password": {"The password field is required."}}
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PaginatedResponse",
 *     type="object",
 *     title="Paginated Response",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="data", type="array", @OA\Items(type="object")),
 *     @OA\Property(
 *         property="meta",
 *         type="object",
 *         @OA\Property(property="total", type="integer", example=100),
 *         @OA\Property(property="per_page", type="integer", example=15),
 *         @OA\Property(property="current_page", type="integer", example=1),
 *         @OA\Property(property="last_page", type="integer", example=7)
 *     )
 * )
 */

declare(strict_types=1);

namespace App\Core;

// ... rest of class
```

---

### Step 11: Repeat for Other Modules

Apply the same annotation pattern to all 6 modules:

- **Families:** MemberProfileController, FamilyController, AddressController, TrainerController, AdminController, EntityController
- **Academics:** ProgramController, BatchController, SessionController, AttendanceController, EnrollmentController
- **Payments:** PaymentController
- **Community:** GroupController, InvitationController
- **Notifications:** NotificationController, ActivityLogController

Each controller method should have:
- `@OA\Get/Post/Put/Patch/Delete`
- `operationId` (unique identifier)
- `tags` (for grouping)
- `summary` and `description`
- `@OA\RequestBody` (for POST/PUT/PATCH)
- All possible `@OA\Response` codes
- `security={{"bearerAuth":{}}}` if protected route

---

### Step 12: Update .gitignore

Add to **`.gitignore`**:

```
# Swagger documentation (auto-generated)
public/swagger/swagger.json
```

The generated JSON file should not be committed; it's regenerated before deployment.

---

## Routing Architecture

After implementation, your routes will be:

```
GET    /swagger                    → Swagger UI HTML page
GET    /swagger/swagger.json       → OpenAPI specification (JSON)

GET    /api/v1/auth/login          → Your API endpoints
GET    /api/v1/auth/me
... (all other API endpoints)
```

**Advantages of Option 1 (Separate routes file):**
✅ Clean architectural separation  
✅ Documentation routes isolated from API routes  
✅ Easy to enable/disable docs per environment  
✅ Scalable if documentation grows  

---

## Workflow

### For Development

1. **Annotate controllers and DTOs** as you build new features
2. **Generate docs on demand:**
   ```bash
   composer docs
   ```
3. **View documentation:**
   ```
   http://localhost:8080/swagger
   ```

### For Production

1. **Generate docs in CI/CD pipeline** before deployment:
   ```bash
   composer docs
   ```
2. **Serve static Swagger UI** from CDN or static hosting
3. **Optionally commit swagger.json** to version control for faster CI

---

## Testing the Implementation

1. **Generate swagger.json:**
   ```bash
   cd kcdf-api-backend
   composer docs
   ```

2. **Start the API server:**
   ```bash
   composer start
   ```

3. **Open in browser:**
   ```
   http://localhost:8080/swagger
   ```

4. **Verify:**
   - Swagger UI loads successfully
   - All endpoints appear in the UI
   - Request/response schemas are visible
   - You can expand each endpoint to see full details
   - Authentication flows work correctly

---

## Additional Resources

- **swagger-php Documentation:** https://zircote.com/swagger-php/
- **OpenAPI 3.0 Spec:** https://spec.openapis.org/oas/v3.0.3
- **Swagger UI Guide:** https://swagger.io/tools/swagger-ui/

---

## Deliverables

Upon completion, you should have:

✅ Composer package `zircote/swagger-php` installed  
✅ OpenAPI config file (`config/openapi.php`)  
✅ Swagger generation script (`scripts/generate-swagger.php`)  
✅ Documentation routes file (`routes/documentation.php`) — OPTION 1  
✅ Updated `public/index.php` loading documentation routes  
✅ Swagger UI HTML endpoint (`public/swagger/swagger-ui.html`)  
✅ Documentation controller (`src/Modules/Documentation/Controllers/DocumentationController.php`)  
✅ All 6 modules' controllers annotated with OpenAPI  
✅ All DTOs/Models documented with `@OA\Schema`  
✅ Common response schemas defined  
✅ `composer docs` script ready to generate  
✅ Generated `public/swagger/swagger.json` visible at `/swagger`  
✅ `.gitignore` updated to exclude swagger.json  

---

## Notes

- **Annotation complexity is worth it** — Swagger UI provides interactive documentation that auto-updates with code
- **Keep examples realistic** — Use actual data in `@OA\Property` examples
- **Test all status codes** — Don't just document happy paths; include error responses
- **Run `composer docs` before committing** — Keep swagger.json in sync with code
- **No breaking changes** — Adding annotations does not modify API behavior
- **OPTION 1 chosen:** Documentation routes in separate file (`routes/documentation.php`) for clean architectural separation
