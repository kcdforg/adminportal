# KCDF Parents — Admin Portal

Angular SPA for KCDF staff. Manages members, families, academics, payments, community, and reports.

**Dev URL:** `http://localhost:4200`  
**Production API:** `https://api.kcdfindia.com/api/v1`

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Angular 21 (standalone components) |
| UI | Tailwind CSS 4 utilities and native Angular controls |
| State | Angular Signals |
| HTTP | HttpClient + JWT interceptors |
| API | KCDF REST API (`kcdf-api-backend`) |

---

## Prerequisites

- Node.js `^20.19.0`, `^22.12.0`, or `>=24.0.0` and npm (the project declares npm `11.4.2`)
- KCDF API backend running at `http://localhost:8080/api/v1`
- Admin user account in the API (`member_profiles` + `user_logins` + `admins` record)

---

## Installation

```bash
cd kcdf-admin-app
npm install
```

### API URL configuration

Development (`src/environments/environment.ts`):

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8080/api/v1'
};
```

Production (`src/environments/environment.prod.ts`, as currently configured):

```typescript
export const environment = {
  production: true,
  apiUrl: 'https://api.kcdfindia.com/api/v1'
};
```

If the production API host changes, update this environment value before building for production.

### UI styling

Tailwind CSS 4 is configured through `.postcssrc.json` and `src/tailwind.css`. Tailwind preflight remains disabled to preserve the application's existing global element styles. The UI uses native Angular form controls, accessible Tailwind dialogs, toasts, tables, pagination, and inline SVG icons; Angular Material and CDK are not runtime dependencies.

Each standalone component keeps its Tailwind utility classes in a neighboring HTML template and has a neighboring SCSS file for component-specific styles. Tailwind utilities remain generated globally from the templates.

---

## Development

Start the API backend first (see `../kcdf-api-backend/README.md`), then:

```bash
npm start
```

Open **http://localhost:4200**

Default Angular dev server port is `4200`. Ensure this URL is listed in the API `.env`:

```
CORS_ALLOWED_ORIGINS=http://localhost:4200,http://localhost:8100
```

---

## Build

```bash
# Development build
npm run build -- --configuration development

# Production build
npm run build
```

Output: `dist/kcdf-admin-app/browser/`

Deploy the contents of `dist/kcdf-admin-app/browser/` to the configured static web host.

For SPA routing, configure the server to rewrite all routes to `index.html`.

---

## Features

| Section | Routes | Admin roles |
|---|---|---|
| Dashboard | `/dashboard` | All admins |
| Members | `/members`, `/members/new`, `/members/:id` | All admins |
| Families | `/families`, `/families/new`, `/families/:id` | All admins |
| Trainers | `/trainers`, `/trainers/:id` | `super_admin`, `program_manager` |
| Programs | `/programs` | `super_admin`, `program_manager` |
| Batches | `/batches`, `/batches/new`, `/batches/:id` | `super_admin`, `program_manager` |
| Enrollments | `/enrollments`, `/enrollments/new` | `super_admin`, `program_manager`, `accounts` |
| Payments | `/payments`, `/payments/new` | `super_admin`, `accounts` |
| Groups | `/groups`, `/groups/:id` | `super_admin` |
| Notifications | `/notifications`, `/notifications/send` | `super_admin`, `program_manager` |
| Reports | `/reports/attendance`, `/reports/payments`, `/reports/enrollments` | Role-dependent |
| Audit logs | `/audit-logs` | `super_admin` only |

Role-based access is enforced via route guards (`authGuard`, `roleGuard`).

---

## Project Structure

```
kcdf-admin-app/src/app/
├── core/
│   ├── services/       # API services (auth, members, families, etc.)
│   ├── guards/         # authGuard, roleGuard
│   ├── interceptors/   # JWT + error handling
│   ├── models/         # TypeScript interfaces
│   └── store/          # Auth state (signals)
├── shared/
│   └── components/     # Tailwind controls, dialogs, toasts, tables, and pagination
├── layout/
│   ├── main-layout/    # Responsive Tailwind application shell
│   └── sidebar/        # Navigation
└── features/
    ├── auth/login/
    ├── dashboard/
    ├── members/
    ├── families/
    ├── trainers/
    ├── programs/
    ├── batches/
    ├── sessions/
    ├── enrollments/
    ├── payments/
    ├── groups/
    ├── notifications/
    ├── reports/
    └── audit-logs/
```

---

## Authentication Flow

1. User logs in at `/login` → `POST /api/v1/auth/login`
2. Access + refresh tokens stored in `localStorage`
3. `jwt.interceptor` attaches `Authorization: Bearer <token>` to all requests
4. On 401, `error.interceptor` attempts silent token refresh
5. On refresh failure → redirect to `/login`

Admin roles are read from the JWT payload and used by `roleGuard` on protected routes.

---

## Login Requirements

The logged-in user must have:

1. A `user_logins` record (active)
2. An `admins` record with one of: `super_admin`, `program_manager`, `accounts`, `readonly`

Users without an admin role cannot access the portal.

---

## Troubleshooting

| Problem | Fix |
|---|---|
| Cannot connect to API | Ensure `kcdf-api-backend` is running on port 8080 |
| CORS error | Add `http://localhost:4200` to API `CORS_ALLOWED_ORIGINS` |
| Login returns 401 | Verify username/password and `user_logins.is_active = 1` |
| Redirected after login | User may lack an `admins` record or required role |
| Blank page after deploy | Configure server SPA fallback to `index.html` |

---

## Documentation

| Document | Location |
|---|---|
| Admin portal spec | `../docs/frontend/admin-portal.md` |
| API conventions | `../docs/02-api-conventions.md` |
| Module API specs | `../docs/modules/` |

---

## License

Proprietary — KCDF Parents Platform
