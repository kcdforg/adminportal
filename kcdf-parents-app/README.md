# KCDF Parents — Parent App

Ionic + Angular mobile/web application for parents and students. Connects to the shared KCDF REST API.

**Dev URL:** `http://localhost:8100` (Ionic default)  
**Production:** `https://parents.xyz.com`

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Ionic 8 + Angular 20 |
| Components | Standalone components |
| Native | Capacitor 8 (optional — for iOS/Android builds) |
| HTTP | HttpClient + JWT interceptors |
| API | KCDF REST API (`kcdf-api-backend`) |

---

## Prerequisites

- Node.js 20+ and npm
- Ionic CLI (optional but recommended): `npm install -g @ionic/cli`
- KCDF API backend running at `http://localhost:8080/api/v1`
- Parent/student user account in the API

---

## Installation

```bash
cd kcdf-parents-app
npm install
```

### API URL configuration

Add `apiUrl` to the environment files (if not already present):

**`src/environments/environment.ts`** (development):

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8080/api/v1'
};
```

**`src/environments/environment.prod.ts`** (production):

```typescript
export const environment = {
  production: true,
  apiUrl: 'https://api.xyz.com/api/v1'
};
```

Ensure the API `.env` allows this origin:

```
CORS_ALLOWED_ORIGINS=http://localhost:4200,http://localhost:8100
```

---

## Development

Start the API backend first (see `../kcdf-api-backend/README.md`), then:

```bash
npm start
```

Or with Ionic CLI:

```bash
ionic serve
```

Open **http://localhost:8100**

---

## Build

```bash
# Web build
npm run build

# Production build
npm run build -- --configuration production
```

Output: `www/` (Ionic/Angular build output)

### Capacitor (native apps)

```bash
# Add platforms (first time)
npx cap add ios
npx cap add android

# After each web build, sync to native projects
npm run build
npx cap sync
```

Open in native IDEs:

```bash
npx cap open ios
npx cap open android
```

---

## Planned Features

Per [`../docs/frontend/parent-app.md`](../docs/frontend/parent-app.md):

| Feature | Description |
|---|---|
| Auth | Login, logout, JWT refresh |
| Dashboard | Summary cards, quick links |
| Family | View family, add/edit members |
| Academics | Enrollments, batches, sessions, attendance |
| Payments | Payment history, donations |
| Groups | Join/leave parent groups |
| Notifications | In-app notification list |
| Invitations | Invite other parents, accept invite flow |

### Role-based UI

| Role | Access |
|---|---|
| `family_primary` | Full family management, enrollments, payments |
| `family_normal` | View family, join groups, send invitations |
| `family_student` | Own attendance, schedules only |

---

## Project Structure (target)

```
kcdf-parents-app/src/app/
├── core/
│   ├── services/       # auth, family, academics, payments, etc.
│   ├── guards/         # authGuard, roleGuard
│   ├── interceptors/   # JWT + error handling
│   └── models/         # TypeScript interfaces
├── shared/
│   └── components/     # LoadingSpinner, EmptyState, StatusBadge
└── features/
    ├── auth/login/
    ├── dashboard/
    ├── family/
    ├── academics/
    ├── payments/
    ├── groups/
    ├── notifications/
    └── invitations/
```

---

## Authentication

Parents and students log in with credentials from `user_logins`. The JWT payload includes:

- `roles` — e.g. `family_primary`, `family_student`, `trainer`
- `family_ids` — families the user belongs to

Tokens are stored in `localStorage` and attached to API requests via the JWT interceptor.

---

## Login Requirements

The logged-in user must have:

1. A `member_profiles` record
2. An active `user_logins` record
3. A `family_members` record linking them to a family (for parent features)

---

## Production Deployment

1. Set `apiUrl` in `environment.prod.ts` to `https://api.xyz.com/api/v1`
2. Run `npm run build -- --configuration production`
3. Deploy `www/` to `parents.xyz.com`
4. For native apps, build via Capacitor and publish to App Store / Play Store

---

## Troubleshooting

| Problem | Fix |
|---|---|
| Cannot connect to API | Ensure API is running; check `apiUrl` in environment |
| CORS error | Add `http://localhost:8100` to API `CORS_ALLOWED_ORIGINS` |
| Login fails | Verify user has `user_logins` and is linked to a family |
| `ionic` command not found | Use `npm start` or install CLI: `npm i -g @ionic/cli` |
| Capacitor sync issues | Run `npm run build` before `npx cap sync` |

---

## Documentation

| Document | Location |
|---|---|
| Parent app spec | `../docs/frontend/parent-app.md` |
| API conventions | `../docs/02-api-conventions.md` |
| Module API specs | `../docs/modules/` |
| Phase execution guide | `../PHASE_EXECUTION_GUIDE.md` |

---

## License

Proprietary — KCDF Parents Platform
