# KCDF Parents — API Conventions

## Base URL

```
https://api.xyz.com/api/v1
```

All endpoints are prefixed with `/api/v1`. Breaking changes increment to `/api/v2`.

---

## Authentication

### JWT Access Token

- Issued on login
- Short-lived: **15 minutes**
- Sent in `Authorization` header: `Bearer <token>`

### Refresh Token

- Issued on login alongside access token
- Long-lived: **30 days**
- Sent in request body to `/api/v1/auth/refresh`
- Invalidated on logout

### JWT Payload

```json
{
  "sub": 42,
  "profile_id": 42,
  "username": "john.doe",
  "roles": ["family_primary", "trainer"],
  "family_ids": [1, 3],
  "iat": 1700000000,
  "exp": 1700000900
}
```

### Roles in JWT

| Role Value | Assigned When |
|---|---|
| `family_primary` | member_role = primary in any family |
| `family_normal` | member_role = normal in any family |
| `family_student` | member_role = student in any family |
| `trainer` | has a record in trainers table |
| `admin_super` | admin_role = super_admin |
| `admin_program_manager` | admin_role = program_manager |
| `admin_accounts` | admin_role = accounts |
| `admin_readonly` | admin_role = readonly |

---

## Standard Response Envelope

All responses use a consistent envelope format.

### Success

```json
{
  "success": true,
  "data": { ... },
  "message": "Operation successful"
}
```

### Paginated List

```json
{
  "success": true,
  "data": [ ... ],
  "meta": {
    "total": 150,
    "per_page": 20,
    "current_page": 1,
    "last_page": 8
  }
}
```

### Error

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_FAILED",
    "message": "The given data was invalid.",
    "details": {
      "email": ["The email field is required."],
      "mobile": ["The mobile must be a valid phone number."]
    }
  }
}
```

---

## HTTP Status Codes

| Code | When to use |
|---|---|
| 200 | Successful GET, PUT, PATCH |
| 201 | Successful POST (resource created) |
| 204 | Successful DELETE (no response body) |
| 400 | Bad request (malformed input) |
| 401 | Unauthenticated (missing or invalid JWT) |
| 403 | Authenticated but not authorized for this resource |
| 404 | Resource not found |
| 422 | Validation failed (field-level errors in `details`) |
| 429 | Rate limit exceeded |
| 500 | Internal server error |

---

## Pagination

All list endpoints support pagination via query parameters:

```
GET /api/v1/families?page=2&per_page=20
```

Defaults: `page=1`, `per_page=20`. Maximum: `per_page=100`.

---

## Filtering and Sorting

```
GET /api/v1/members?status=active&sort=last_name&order=asc
GET /api/v1/payments?family_id=5&payment_type=donation
GET /api/v1/sessions?batch_id=3&session_date_from=2026-01-01&session_date_to=2026-06-30
```

Standard filter parameters:
- `status` — filter by status value
- `sort` — column to sort by
- `order` — `asc` or `desc` (default: `asc`)
- Date ranges: `{field}_from` and `{field}_to`

---

## Endpoint Map

```
/api/v1/auth/login
/api/v1/auth/logout
/api/v1/auth/refresh
/api/v1/auth/me

/api/v1/members
/api/v1/members/{id}

/api/v1/families
/api/v1/families/{id}
/api/v1/families/{id}/members

/api/v1/trainers
/api/v1/trainers/{id}

/api/v1/admins
/api/v1/admins/{id}

/api/v1/entities
/api/v1/entities/{id}
/api/v1/members/{id}/entity-relations

/api/v1/programs
/api/v1/programs/{id}

/api/v1/batches
/api/v1/batches/{id}
/api/v1/batches/{id}/members
/api/v1/batches/{id}/sessions

/api/v1/sessions/{id}
/api/v1/sessions/{id}/attendance

/api/v1/enrollments
/api/v1/enrollments/{id}

/api/v1/attendance/{id}

/api/v1/payments
/api/v1/payments/{id}
/api/v1/families/{id}/payments

/api/v1/groups
/api/v1/groups/{id}
/api/v1/groups/{id}/members

/api/v1/invitations
/api/v1/invitations/{code}/accept

/api/v1/notifications
/api/v1/notifications/{id}/read

/api/v1/reports/attendance
/api/v1/reports/payments
/api/v1/reports/enrollments
```

---

## Validation Rules

Every write endpoint (POST/PUT/PATCH) must validate input and return 422 on failure.

### Common Field Validations

| Field | Rule |
|---|---|
| `first_name`, `last_name` | required, string, max 100 chars |
| `email` | valid email format, unique in `member_profiles` |
| `mobile` | digits only, 10 digits minimum |
| `date_of_birth` | valid date, not in the future |
| `gender` | must be one of: `male`, `female`, `other` |
| `status` | must be a valid ENUM value for that table |
| `amount` | numeric, min 0, max 2 decimal places |
| `fee_amount` | numeric, min 0 |

---

## Error Codes (Application-level)

| Code | Meaning |
|---|---|
| `VALIDATION_FAILED` | One or more fields failed validation |
| `UNAUTHENTICATED` | No valid JWT provided |
| `UNAUTHORIZED` | JWT valid but role/permission denied |
| `NOT_FOUND` | Resource does not exist |
| `DUPLICATE_ENTRY` | Unique constraint would be violated |
| `ATTENDANCE_LOCKED` | Attendance cannot be modified for a locked session |
| `BATCH_FULL` | Batch capacity reached, enrollment rejected |
| `ENROLLMENT_EXISTS` | Member already enrolled in this batch |
| `INVALID_INVITE_CODE` | Invitation code not found or expired |
| `SERVER_ERROR` | Unexpected internal error |

---

## Middleware Stack (per request)

```
Request
  → CORS Middleware
  → JSON Body Parser
  → JWT Authentication Middleware (protected routes only)
  → Role Authorization Middleware (role-restricted routes only)
  → Route Handler (Controller)
  → Response
```

---

## Rate Limiting

| Endpoint | Limit |
|---|---|
| `POST /api/v1/auth/login` | 10 requests / minute per IP |
| `POST /api/v1/auth/refresh` | 20 requests / minute per IP |
| All other endpoints | 200 requests / minute per authenticated user |

---

## CORS

Allowed origins:
- `https://parents.xyz.com`
- `https://admin.xyz.com`

Allowed methods: `GET, POST, PUT, PATCH, DELETE, OPTIONS`
Allowed headers: `Content-Type, Authorization`

---

## Versioning Policy

- Current version: `v1`
- Breaking changes (removed fields, changed types, removed endpoints) → new version `v2`
- Additive changes (new optional fields, new endpoints) → same version, no breaking change
- Old versions supported for minimum 6 months after new version release
