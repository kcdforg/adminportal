# Auth Module

## Scope

Handles person identity (`member_profiles`), authentication (`user_logins`), JWT issuance, token refresh, and role-based authorization.

---

## Tables

- `member_profiles` — universal person record
- `user_logins` — login credentials linked to a profile

See `docs/01-database.md` for full schema with MySQL types.

---

## Business Rules

1. Every person in the system is represented by exactly one `member_profiles` record.
2. A profile without a `user_logins` record cannot authenticate.
3. One profile can have at most one `user_logins` record.
4. Username must be unique across all logins.
5. Passwords are stored as bcrypt hashes with cost factor 12 minimum.
6. A deactivated login (`is_active = 0`) cannot authenticate even with a valid password.
7. JWT access tokens expire in 15 minutes.
8. Refresh tokens expire in 30 days and are single-use (rotate on each refresh).
9. On logout, the refresh token is invalidated server-side.
10. The JWT payload must include `profile_id`, `username`, `roles[]`, and `family_ids[]`.
11. Roles in the JWT are derived at login time from the profile's active associations (family_members, trainers, admins).
12. Role changes take effect on the next login or token refresh — not immediately.
13. `last_login_at` is updated on every successful authentication.

---

## Role Resolution Logic

When a user logs in, roles are assembled from:

| Source Table | Condition | Role Granted |
|---|---|---|
| `family_members` | member_role = primary | `family_primary` |
| `family_members` | member_role = normal | `family_normal` |
| `family_members` | member_role = student | `family_student` |
| `trainers` | status = active | `trainer` |
| `admins` | admin_role = super_admin | `admin_super` |
| `admins` | admin_role = program_manager | `admin_program_manager` |
| `admins` | admin_role = accounts | `admin_accounts` |
| `admins` | admin_role = readonly | `admin_readonly` |

A single profile can have multiple roles simultaneously (e.g., a parent who is also a trainer).

---

## Access Control

| Action | Who Can |
|---|---|
| Login | Anyone with active user_login |
| Refresh token | Any authenticated user |
| View own profile (`/auth/me`) | Any authenticated user |
| Create member profile | Admin only |
| Create user login | Admin only |
| Deactivate a login | Admin (super_admin, program_manager) |
| Update own profile fields | Authenticated user (own profile only) |

---

## API Endpoints

### POST /api/v1/auth/login

**Auth:** None

**Request:**
```json
{
  "username": "john.doe",
  "password": "SecurePass123"
}
```

**Response 200:**
```json
{
  "success": true,
  "data": {
    "access_token": "eyJ...",
    "refresh_token": "eyJ...",
    "token_type": "Bearer",
    "expires_in": 900,
    "profile": {
      "id": 42,
      "first_name": "John",
      "last_name": "Doe",
      "roles": ["family_primary", "trainer"]
    }
  }
}
```

**Errors:**
- 422 — missing username or password
- 401 — invalid credentials
- 401 — account deactivated

---

### POST /api/v1/auth/refresh

**Auth:** None (refresh token in body)

**Request:**
```json
{
  "refresh_token": "eyJ..."
}
```

**Response 200:** Same as login response (new access + refresh token pair)

**Errors:**
- 401 — invalid or expired refresh token

---

### POST /api/v1/auth/logout

**Auth:** Bearer token required

**Request:**
```json
{
  "refresh_token": "eyJ..."
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

### GET /api/v1/auth/me

**Auth:** Bearer token required

**Response 200:**
```json
{
  "success": true,
  "data": {
    "id": 42,
    "first_name": "John",
    "middle_name": null,
    "last_name": "Doe",
    "email": "john@example.com",
    "mobile": "9876543210",
    "gender": "male",
    "date_of_birth": "1985-06-15",
    "photo_url": null,
    "blood_group": "O+",
    "status": "active",
    "roles": ["family_primary", "trainer"],
    "family_ids": [1]
  }
}
```

---

## Validation Rules

| Field | Rule |
|---|---|
| `username` | required, string, min 3 chars, max 100 chars, no spaces |
| `password` | required, string, min 8 chars |
| `refresh_token` | required for refresh and logout endpoints |

---

## Module Folder Structure

```
src/Modules/Auth/
├── Controllers/
│   └── AuthController.php
├── Services/
│   └── AuthService.php
├── Repositories/
│   ├── ProfileRepository.php
│   └── UserLoginRepository.php
├── Models/
│   ├── MemberProfile.php
│   └── UserLogin.php
├── DTOs/
│   ├── LoginRequestDTO.php
│   └── AuthResponseDTO.php
├── Validators/
│   └── LoginValidator.php
└── Middleware/
    ├── JwtAuthMiddleware.php
    └── RoleMiddleware.php
```
