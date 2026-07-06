# KCDF Parents — System Overview

## Purpose

KCDF Parents is a community platform for managing families, students, trainers, academic programs, payments, and community features for the KCDF organization.

---

## Applications

| Application | Repo | Domain | Tech |
|---|---|---|---|
| Parent App | `kcdf-parents-app` | Mobile/web for parents and students | Ionic + Angular |
| Admin Portal | `kcdf-admin-app` | Desktop admin for staff | Angular + Angular Material |
| API Backend | `kcdf-api-backend` | Shared REST API | Slim Framework 4 + MySQL 8 |

---

## Deployment

```
parents.xyz.com   → kcdf-parents-app  (Ionic Angular)
admin.xyz.com     → kcdf-admin-app    (Angular SPA)
api.xyz.com       → kcdf-api-backend  (Slim Framework 4)
```

Each application is deployed independently. The Angular apps communicate exclusively with the Slim API over HTTPS.

---

## Tech Stack

### Frontend
- Angular (latest stable)
- Ionic + Angular — Parent App
- Angular Material — Admin Portal UI
- Angular Signals for reactive state
- Standalone components

### Backend
- PHP 8.2+
- Slim Framework 4
- REST API architecture
- JWT authentication (access + refresh tokens)
- PHP-DI for dependency injection

### Database
- MySQL 8.0+
- Soft deletes via `status` ENUM fields
- JSON columns for flexible metadata
- Indexes on all FK columns and common filter columns

### Future / Planned
- Redis — response caching, session store
- Queue workers — async jobs (notifications, emails)
- MySQL read replicas — reporting queries
- Horizontal scaling — multiple API instances behind a load balancer
- Microservice extraction per domain module

---

## Architectural Principles

1. **Modular monolith first** — Single Slim API, internally divided by domain. No premature microservices.
2. **Person-based identity** — `member_profiles` is the universal person record. Business roles are separate tables referencing it.
3. **Thin controllers** — Business logic lives in Services, not Controllers.
4. **Repository pattern** — All DB access goes through Repository classes. Services never write raw SQL.
5. **Clean API boundaries** — Each module exposes a clean API surface so it can be extracted as a microservice later.
6. **Generic over specific** — Use `entities` + `entity_member_relations` instead of separate tables for schools, colleges, etc.
7. **JSON fields sparingly** — Only for genuinely flexible metadata (e.g. `relation_context`, `meta`). Never for queryable data.
8. **No over-engineering** — Build what is needed now. Design for extension, not for speculation.
9. **Production-ready** — Proper validation, error handling, logging, and access control from day one.
10. **Long-term maintainability** — Code must be readable and consistent across all modules.

---

## Module Map

```
kcdf-api-backend/src/Modules/
├── Auth/           → member_profiles, user_logins, JWT, roles
├── Families/       → families, addresses, family_members, trainers, admins, entities, entity_member_relations
├── Academics/      → programs, student_batches, batch_members, batch_sessions, attendance, enrollments
├── Payments/       → payments
├── Community/      → parent_groups, group_members, invitations
└── Notifications/  → notifications, activity_logs
```

---

## Scaling Roadmap

| Phase | Architecture |
|---|---|
| Now | Single Slim API + Single MySQL |
| Near-term | Redis caching on read-heavy endpoints |
| Medium-term | Queue workers for notifications and emails |
| Long-term | MySQL read replicas, multiple API instances, load balancer |
| Future | Microservice extraction per module |

---

## Related Documents

- `docs/01-database.md` — Full table reference
- `docs/02-api-conventions.md` — REST conventions, auth, error format
- `docs/modules/` — Per-module business rules and API specs
- `docs/deployment/backend-installer.md` — Web-based API installer (XAMPP / first-time setup)
- `docs/adr/` — Architecture Decision Records
- `prompts/` — AI execution prompts per phase
