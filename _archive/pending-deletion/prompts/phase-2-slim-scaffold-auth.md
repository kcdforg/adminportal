# Phase 2 — Slim API Scaffold + Auth Module

## Context

You are building the `kcdf-api-backend` — a Slim Framework 4 REST API backend for the KCDF Parents platform.

The database schema is already complete (see `database/schema.sql`).

This phase covers:
1. Slim Framework 4 project scaffold
2. Auth module implementation

Do NOT build any other module in this phase.

---

## Tech Stack

- PHP 8.2+
- Slim Framework 4
- PHP-DI (dependency injection container)
- firebase/php-jwt (JWT library)
- illuminate/database (Eloquent ORM — use only the query builder and model layer, not migrations)
- vlucas/phpdotenv (environment config)
- monolog/monolog (logging)

---

## Project Structure to Generate

```
kcdf-api-backend/
├── composer.json
├── .env.example
├── .gitignore
├── public/
│   └── index.php               ← Slim app entry point
├── database/
│   └── schema.sql              ← already exists from Phase 1
├── config/
│   ├── app.php                 ← app settings
│   ├── database.php            ← DB config
│   └── container.php           ← PHP-DI container definitions
├── src/
│   ├── Core/
│   │   ├── BaseController.php  ← response helper methods
│   │   ├── BaseRepository.php  ← common DB query methods
│   │   └── BaseService.php     ← (optional base)
│   ├── Middleware/
│   │   ├── JwtAuthMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   └── CorsMiddleware.php
│   └── Modules/
│       └── Auth/
│           ├── Controllers/
│           │   └── AuthController.php
│           ├── Services/
│           │   └── AuthService.php
│           ├── Repositories/
│           │   ├── ProfileRepository.php
│           │   └── UserLoginRepository.php
│           ├── Models/
│           │   ├── MemberProfile.php
│           │   └── UserLogin.php
│           ├── DTOs/
│           │   ├── LoginRequestDTO.php
│           │   └── AuthResponseDTO.php
│           ├── Validators/
│           │   └── LoginValidator.php
│           └── routes.php
├── routes/
│   └── api.php                 ← registers all module route files
└── bootstrap/
    └── app.php                 ← creates and configures Slim app
```

---

## API Endpoints to Implement

### POST /api/v1/auth/login

- Validate: username (required), password (required)
- Find user_login by username
- Verify password_hash using password_verify()
- Check is_active = 1
- Build JWT payload with: sub (profile_id), username, roles[], family_ids[]
- Role resolution: query family_members, trainers, admins for this profile_id
- Issue access token (15 min) and refresh token (30 days)
- Update last_login_at
- Return standard response with tokens and profile data

### POST /api/v1/auth/refresh

- Accept refresh_token in request body
- Validate and decode the refresh token
- Issue new access + refresh token pair
- Invalidate old refresh token

### POST /api/v1/auth/logout

- Requires valid access token (Bearer)
- Accept refresh_token in body
- Invalidate refresh token
- Return success

### GET /api/v1/auth/me

- Requires valid access token (Bearer)
- Return authenticated profile data with roles

---

## JWT Specification

```
Access Token:
- Algorithm: HS256
- Expiry: 15 minutes
- Payload: { sub, profile_id, username, roles[], family_ids[], iat, exp }

Refresh Token:
- Algorithm: HS256
- Expiry: 30 days
- Payload: { sub, type: "refresh", iat, exp }
```

JWT secret comes from `.env`: `JWT_SECRET`

For refresh token invalidation in Phase 2, use a simple DB table `refresh_tokens`:
```sql
CREATE TABLE refresh_tokens (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profile_id BIGINT UNSIGNED NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    revoked_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token_hash (token_hash),
    INDEX idx_profile_id (profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Add this CREATE TABLE to the bottom of `database/schema.sql` as well.

---

## Standard API Response Format

All responses must use this envelope. `BaseController` must provide helper methods for this.

```php
// Success
$this->success($response, $data, $message = 'OK', $status = 200)
// → {"success": true, "data": {...}, "message": "OK"}

// List
$this->paginate($response, $data, $meta)
// → {"success": true, "data": [...], "meta": {...}}

// Error
$this->error($response, $code, $message, $details = [], $status = 400)
// → {"success": false, "error": {"code": "...", "message": "...", "details": {...}}}
```

---

## Middleware

### JwtAuthMiddleware
- Reads `Authorization: Bearer <token>` header
- Decodes and validates JWT using firebase/php-jwt
- Sets decoded payload into request attribute: `jwt_payload`
- Returns 401 with `UNAUTHENTICATED` error code if missing or invalid

### RoleMiddleware
- Reads `jwt_payload` from request attributes
- Checks `roles[]` array in payload against required roles
- Returns 403 with `UNAUTHORIZED` error code if role not present
- Usage: `->add(new RoleMiddleware(['admin_super', 'admin_program_manager']))`

### CorsMiddleware
- Sets headers: `Access-Control-Allow-Origin`, `Access-Control-Allow-Methods`, `Access-Control-Allow-Headers`
- Allowed origins from `.env`: `CORS_ALLOWED_ORIGINS`
- Handles OPTIONS preflight requests

---

## .env.example

```
APP_ENV=development
APP_DEBUG=true
APP_NAME="KCDF Parents API"

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kcdf_parents
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=change-this-to-a-secure-random-string
JWT_ACCESS_TTL=900
JWT_REFRESH_TTL=2592000

CORS_ALLOWED_ORIGINS=http://localhost:4200,http://localhost:8100
```

---

## composer.json Dependencies

```json
{
  "require": {
    "php": "^8.2",
    "slim/slim": "^4.0",
    "slim/psr7": "^1.0",
    "php-di/php-di": "^7.0",
    "firebase/php-jwt": "^6.0",
    "illuminate/database": "^11.0",
    "vlucas/phpdotenv": "^5.0",
    "monolog/monolog": "^3.0"
  },
  "require-dev": {
    "phpunit/phpunit": "^11.0"
  }
}
```

---

## Rules

- Thin controllers: controllers only parse input, call service, return response
- No SQL in controllers or services — only in repositories
- Services call repositories, not DB directly
- Use PHP-DI for all dependency injection — no `new` in controllers or services
- All validation errors return 422 with field-level details
- Passwords hashed with password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])
- All DB queries through Eloquent query builder (not raw SQL)
