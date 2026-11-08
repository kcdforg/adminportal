# Phase 7b — Angular Admin Portal

## Context

You are building `kcdf-admin-app` — an Angular SPA with Angular Material for KCDF administrators.
The `kcdf-api-backend` (Phases 1–6) is already complete and running at `https://api.xyz.com`.

This is a new project from scratch.

---

## Tech Stack

- Angular (latest stable)
- Angular Material (latest)
- Standalone components throughout
- Angular Signals for reactive state
- Angular Reactive Forms
- Angular HttpClient

---

## Project Scaffold

```bash
ng new kcdf-admin-app --standalone --routing --style=scss
ng add @angular/material
```

---

## Project Structure

```
kcdf-admin-app/src/app/
├── core/
│   ├── services/
│   │   ├── auth.service.ts
│   │   ├── api.service.ts
│   │   ├── member.service.ts
│   │   ├── family.service.ts
│   │   ├── trainer.service.ts
│   │   ├── program.service.ts
│   │   ├── batch.service.ts
│   │   ├── session.service.ts
│   │   ├── attendance.service.ts
│   │   ├── enrollment.service.ts
│   │   ├── payment.service.ts
│   │   ├── group.service.ts
│   │   ├── notification.service.ts
│   │   └── activity-log.service.ts
│   ├── guards/
│   │   ├── auth.guard.ts
│   │   └── role.guard.ts
│   ├── interceptors/
│   │   ├── jwt.interceptor.ts
│   │   └── error.interceptor.ts
│   ├── models/
│   │   └── (all domain TypeScript interfaces)
│   └── store/
│       └── auth.store.ts
├── shared/
│   ├── components/
│   │   ├── data-table/             ← reusable paginated MatTable wrapper
│   │   ├── confirm-dialog/
│   │   ├── loading-overlay/
│   │   ├── status-badge/
│   │   └── page-header/
│   └── pipes/
│       └── currency-inr.pipe.ts
├── layout/
│   ├── main-layout/
│   │   ├── main-layout.component.ts
│   │   └── main-layout.component.html    ← mat-sidenav-container layout
│   └── sidebar/
│       └── sidebar.component.ts
└── features/
    ├── auth/
    │   └── login/
    ├── dashboard/
    ├── members/
    │   ├── member-list/
    │   ├── member-detail/
    │   └── member-form/
    ├── families/
    │   ├── family-list/
    │   ├── family-detail/
    │   └── family-form/
    ├── trainers/
    │   ├── trainer-list/
    │   ├── trainer-detail/
    │   └── trainer-form/
    ├── programs/
    │   ├── program-list/
    │   └── program-form/
    ├── batches/
    │   ├── batch-list/
    │   ├── batch-detail/
    │   └── batch-form/
    ├── sessions/
    │   ├── session-form/
    │   └── session-attendance/
    ├── enrollments/
    │   ├── enrollment-list/
    │   └── enrollment-form/
    ├── payments/
    │   ├── payment-list/
    │   └── payment-form/
    ├── groups/
    │   ├── group-list/
    │   └── group-detail/
    ├── notifications/
    │   ├── notification-list/
    │   └── send-notification/
    ├── reports/
    │   ├── attendance-report/
    │   ├── payment-report/
    │   └── enrollment-report/
    └── audit-logs/
        └── audit-log-list/
```

---

## Layout

Use `MatSidenavContainer` as the root layout:
- Sidebar: navigation links grouped by section, role-aware (hide items user cannot access)
- Toolbar: app name, logged-in user name + role badge, logout button
- Content area: router-outlet

Sidebar sections:
- People: Members, Families, Trainers, Admins
- Academics: Programs, Batches, Sessions, Enrollments, Attendance
- Finance: Payments
- Community: Groups, Notifications
- Reports: Attendance Report, Payment Report, Enrollment Report
- System: Audit Logs (super_admin only)

---

## Shared DataTable Component

Build a reusable `DataTableComponent` that wraps `MatTable`:

```typescript
@Input() columns: ColumnDef[];       // column definitions: key, label, type
@Input() dataSource: any[];          // data array
@Input() loading: boolean;           // show skeleton rows
@Input() totalCount: number;         // for paginator
@Input() pageSize: number;
@Output() pageChange: EventEmitter;
@Output() rowClick: EventEmitter;
```

Includes: `MatPaginator`, `MatSort`, loading overlay, empty state row.

Use this component on every list page.

---

## Screens to Build

### Login (`/login`)
- MatFormField for username and password
- MatButton to submit
- Error MatSnackBar on failure

### Dashboard (`/dashboard`)
- MatCard summary stats: total families, active batches, pending payments, unread notifications
- Quick navigation MatCards

### Member Management (`/members`)
- List: DataTable with columns: name, email, mobile, status, actions
- Filters: status, search by name
- Create member → slide-out form or dialog
- Member Detail: personal info, family memberships, login status
- Edit member → MatDialog form

### Family Management (`/families`)
- List: DataTable with columns: family_code, family_name, city, member_count, status
- Create family → form with inline address fields
- Family Detail: info, address, member list with roles
- Add/remove members from family

### Trainer Management (`/trainers`)
- List: DataTable with columns: trainer_code, name, specialization, status
- Create trainer: select existing member profile, fill trainer details
- Trainer Detail: profile info, assigned batches, address

### Program Management (`/programs`)
- List: DataTable with columns: name, type, fee, status
- Create/edit: MatDialog form

### Batch Management (`/batches`)
- List: DataTable with columns: batch_name, program, trainer, start_date, status, capacity
- Create batch: select program, trainer, set capacity and dates
- Batch Detail: MatTabGroup with tabs: Info | Members | Sessions
- Sessions tab: list sessions with status badges, add session button

### Session Management
- Session form: MatDialog for create/edit
- Fields: date, start_time, end_time, title, session_type, trainer override, topics_covered, homework, notes
- Session Attendance page: grid of members × status dropdowns, save all button, lock session button

### Enrollment Management (`/enrollments`)
- List: DataTable with columns: member name, batch, family, enrolled_at, status, payment_status
- Filter: family, batch, status, payment_status
- Create enrollment: select family → select member → select batch
- Cancel enrollment: confirm dialog

### Payment Management (`/payments`)
- List: DataTable with columns: family, type, amount, method, status, date
- Filter: family, payment_type, status, date range
- Record payment: MatDialog form
- Record refund: MatDialog form (prefill family/enrollment from existing payment)

### Group Management (`/groups`)
- List: DataTable with group_name, visibility, member_count, status
- Group Detail: info + member list with ban/remove actions

### Notifications (`/notifications`)
- Sent notifications list
- Send notification: form with member multi-select or broadcast target
- Broadcast: select target_type (batch/group/all_families), target, message

### Reports

#### Attendance Report (`/reports/attendance`)
- Filters: batch (required), date range
- Table: member names (rows) × session dates (columns) × status badge
- Export CSV button

#### Payment Report (`/reports/payments`)
- Filters: date range, payment_type, status
- Summary row: total collected, by type, by method
- Transaction table below
- Export CSV

#### Enrollment Report (`/reports/enrollments`)
- Filters: program, batch, status, date range
- Summary: counts by status, counts by payment_status
- Detail table

### Audit Logs (`/audit-logs`) — super_admin only
- DataTable: actor, action, entity_type, entity_id, timestamp
- Click row → MatDialog showing old_values vs new_values JSON diff

---

## Role-Based Navigation

Hide sidebar items and guard routes based on admin role in JWT:

| Route | Roles |
|---|---|
| /members | all admin roles |
| /families | all admin roles |
| /trainers | super_admin, program_manager |
| /programs | super_admin, program_manager |
| /batches | super_admin, program_manager |
| /sessions | super_admin, program_manager |
| /enrollments | super_admin, program_manager, accounts |
| /payments | super_admin, accounts |
| /groups | super_admin |
| /notifications | super_admin, program_manager |
| /reports/attendance | super_admin, program_manager, readonly |
| /reports/payments | super_admin, accounts, readonly |
| /audit-logs | super_admin |

---

## Authentication

Same pattern as parent app:
- Store tokens in localStorage
- `AuthService` with signals: `currentUser`, `isAuthenticated`, `adminRole`
- JWT interceptor attaches Bearer token
- Error interceptor handles 401 refresh flow
- `auth.guard.ts` protects all routes
- `role.guard.ts` enforces role restrictions per route

---

## Angular Conventions

- All components standalone
- `inject()` for DI
- Typed Reactive Forms (`FormGroup<{field: FormControl<type>}>`)
- All API model types in `core/models/`
- Lazy load all feature routes
- Use `MatSnackBar` for all toast messages
- Use `MatDialog` for confirm dialogs and quick forms
- Use `MatProgressBar` at top of page during loading
- Export CSV using Blob + anchor click (no external library needed)

---

## Environment

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8080/api/v1'
};
```
