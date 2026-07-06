# KCDF Parents — Phase Execution Guide

Use this file as a checklist. For each phase: start a **new chat**, paste the **@ references** below, and work only in the folder indicated.

**Workspace:** `kcdf-parents/` (all repos live here)

---

## How to Start Each Phase (Cursor @ References)

This is the pattern that worked for Phase 3. For every phase, paste **exactly** these lines into a new chat (Cursor will attach the files/folders as context):

```
@prompts/phase-N-....md
@docs/...                          ← module or frontend spec (see per-phase list below)
@kcdf-api-backend                  ← or @kcdf-parents-app / @kcdf-admin-app for Phase 7

Implement Phase N. Work only in the target folder. Follow the attached prompt and docs.
```

**Why this works:** `@prompts/` gives the AI the execution instructions; `@docs/` gives business rules and API shapes; `@kcdf-api-backend` (or frontend folder) scopes where to write code.

**Optional extra references** (add if the agent needs more context):

- `@docs/01-database.md` — table definitions
- `@docs/02-api-conventions.md` — response format, auth, errors
- `@.cursor/rules/project-conventions.mdc` — architecture rules (planning repo)
- `@kcdf-api-backend/.cursor/rules/backend-conventions.mdc` — backend rules

| Folder | Purpose |
|---|---|
| `kcdf-api-backend/` | Slim Framework 4 REST API |
| `kcdf-parents-app/` | Ionic + Angular Parent App |
| `kcdf-admin-app/` | Angular + Material Admin Portal |
| `docs/` | Source of truth (read + update when requirements change) |
| `prompts/` | AI execution prompts (paste into new chats) |

---

## Completed Phases

| Phase | Status | Prompt file | Output |
|---|---|---|---|
| 1 — Database Schema | Done | `prompts/phase-1-database-schema.md` | `kcdf-api-backend/database/schema.sql` |
| 2 — Slim Scaffold + Auth | Done | `prompts/phase-2-slim-scaffold-auth.md` | API scaffold, JWT auth module |
| 3 — Families + Members | Done | `prompts/phase-3-families-members.md` | Families, members, trainers, admins, entities |

---

## Remaining Phases

| Phase | What to build | Prompt file | Target folder |
|---|---|---|---|
| **4** | Academics (programs, batches, sessions, attendance, enrollments) | `prompts/phase-4-academics.md` | `kcdf-api-backend/` |
| **5** | Payments | `prompts/phase-5-payments.md` | `kcdf-api-backend/` |
| **6** | Community + Notifications + Audit logs | `prompts/phase-6-community-notifications.md` | `kcdf-api-backend/` |
| **7a** | Ionic Parent App | `prompts/phase-7a-parent-app.md` | `kcdf-parents-app/` |
| **7b** | Angular Admin Portal | `prompts/phase-7b-admin-portal.md` | `kcdf-admin-app/` |

**Order rule:** Complete phases 4 → 5 → 6 before starting 7a or 7b. Frontends depend on a working API.

---

## Before Each Backend Phase

For a **fresh environment**, use the web installer first: [`docs/deployment/backend-installer.md`](docs/deployment/backend-installer.md)

```bash
cd kcdf-api-backend
composer install
# Then open http://localhost:8080/install/ in the browser
```

For development without the installer:

```bash
cd kcdf-api-backend
source ../scripts/env-xampp.sh    # XAMPP PHP on PATH (macOS)
composer install                  # first time only
composer start                    # http://localhost:8080
```

Ensure MySQL is running (XAMPP) and schema is imported:

```bash
mysql -u root -p kcdf_parents < database/schema.sql
```

---

## Phase 4 — Academics Module

**Paste into a new chat:**

```
@prompts/phase-4-academics.md
@docs/modules/academics.md
@docs/02-api-conventions.md
@kcdf-api-backend

Implement Phase 4 — Academics module. Work only in kcdf-api-backend.
Do NOT implement Payments, Community, or Notifications.
Register routes in routes/api.php when done.
```

**Optional split** (if output is too large):

- Chat A: Programs + Batches + Sessions  
- Chat B: Attendance + Enrollments  

**Smoke test after Phase 4:**

- `POST /api/v1/programs`
- `POST /api/v1/batches`
- `POST /api/v1/batches/{id}/sessions`
- `POST /api/v1/enrollments`
- `POST /api/v1/sessions/{id}/attendance`

---

## Phase 5 — Payments Module

**Paste into a new chat:**

```
@prompts/phase-5-payments.md
@docs/modules/payments.md
@docs/02-api-conventions.md
@kcdf-api-backend

Implement Phase 5 — Payments module. Work only in kcdf-api-backend.
Do NOT implement Community or Notifications.
Register routes in routes/api.php when done.
```

**Smoke test after Phase 5:**

- `POST /api/v1/payments` (class_fee with enrollment_id)
- `GET /api/v1/families/{id}/payments`
- Verify `enrollment.payment_status` updates (unpaid → partial → paid)

---

## Phase 6 — Community + Notifications

**Paste into a new chat:**

```
@prompts/phase-6-community-notifications.md
@docs/modules/community.md
@docs/modules/notifications.md
@docs/02-api-conventions.md
@kcdf-api-backend

Implement Phase 6 — Community and Notifications modules. Work only in kcdf-api-backend.
Register routes in routes/api.php when done.
```

**Smoke test after Phase 6:**

- `POST /api/v1/groups`, `POST /api/v1/groups/{id}/join`
- `POST /api/v1/invitations`, `GET /api/v1/invitations/{code}`
- `GET /api/v1/notifications`, `POST /api/v1/notifications/send`
- `GET /api/v1/activity-logs` (super_admin)

---

## Phase 7a — Ionic Parent App

**Spec:** `docs/frontend/parent-app.md`

**Prerequisite:** Backend phases 1–6 complete and API tested.

**Scaffold (first time):**

```bash
cd kcdf-parents
ionic start kcdf-parents-app blank --type=angular --standalone
```

**Paste into a new chat:**

```
@prompts/phase-7a-parent-app.md
@docs/frontend/parent-app.md
@docs/02-api-conventions.md
@kcdf-parents-app

Implement Phase 7a — Ionic Parent App. Work only in kcdf-parents-app.
API base URL (dev): http://localhost:8080/api/v1
```

---

## Phase 7b — Angular Admin Portal

**Spec:** `docs/frontend/admin-portal.md`

**Prerequisite:** Backend phases 1–6 complete and API tested.

**Scaffold (first time):**

```bash
cd kcdf-parents
ng new kcdf-admin-app --standalone --routing --style=scss
cd kcdf-admin-app
ng add @angular/material
```

**Paste into a new chat:**

```
@prompts/phase-7b-admin-portal.md
@docs/frontend/admin-portal.md
@docs/02-api-conventions.md
@kcdf-admin-app

Implement Phase 7b — Angular Admin Portal. Work only in kcdf-admin-app.
API base URL (dev): http://localhost:8080/api/v1
```

---

## After Each Phase

1. Test the new endpoints or screens manually (or with curl/Postman).
2. Fix any issues before starting the next phase.
3. If requirements changed during implementation, update `docs/modules/` (and `docs/adr/` if it was a design decision).

---

## Quick Reference — @ References Per Phase

| Phase | Paste these @ references | One-line instruction |
|---|---|---|
| 1 ✓ | `@prompts/phase-1-database-schema.md` `@docs/01-database.md` `@kcdf-api-backend` | Generate `database/schema.sql` |
| 2 ✓ | `@prompts/phase-2-slim-scaffold-auth.md` `@docs/modules/auth.md` `@docs/02-api-conventions.md` `@kcdf-api-backend` | Slim scaffold + Auth module |
| 3 ✓ | `@prompts/phase-3-families-members.md` `@docs/modules/families.md` `@kcdf-api-backend` | Families module |
| **4** | `@prompts/phase-4-academics.md` `@docs/modules/academics.md` `@kcdf-api-backend` | Academics module |
| 5 | `@prompts/phase-5-payments.md` `@docs/modules/payments.md` `@kcdf-api-backend` | Payments module |
| 6 | `@prompts/phase-6-community-notifications.md` `@docs/modules/community.md` `@docs/modules/notifications.md` `@kcdf-api-backend` | Community + Notifications |
| 7a | `@prompts/phase-7a-parent-app.md` `@docs/frontend/parent-app.md` `@kcdf-parents-app` | Ionic Parent App |
| 7b | `@prompts/phase-7b-admin-portal.md` `@docs/frontend/admin-portal.md` `@kcdf-admin-app` | Angular Admin Portal |

Add `@docs/02-api-conventions.md` to any backend phase if auth/errors need reinforcement.

---

## Tips

- **New chat per phase** — keeps AI context focused.
- **Always use @ references** — same pattern as Phase 3; do not paste long prose prompts.
- **Do not run `updated-prompt.md` directly** — use phase prompts + docs via `@`.
- **Large phases** — split Phase 4 or 7 into two chats; keep the same @ references, add scope in the one-line instruction (e.g. "Programs + Batches only").
