# Admin Portal — Angular SPA

## Overview

The Admin Portal is an Angular SPA with Angular Material UI. It is deployed at `admin.xyz.com` and is desktop-focused.

**Repo:** `kcdf-admin-app`

---

## Tech Stack

- Angular (latest stable)
- Angular Material
- Angular Signals for state management
- Standalone components
- Angular HttpClient with interceptors

---

## Project Structure

```
kcdf-admin-app/src/
├── app/
│   ├── core/
│   │   ├── services/
│   │   │   ├── auth.service.ts
│   │   │   └── api.service.ts
│   │   ├── guards/
│   │   │   ├── auth.guard.ts
│   │   │   └── role.guard.ts
│   │   ├── interceptors/
│   │   │   ├── jwt.interceptor.ts
│   │   │   └── error.interceptor.ts
│   │   └── models/
│   │       └── (all domain model types)
│   ├── shared/
│   │   ├── components/
│   │   │   ├── data-table/             ← reusable paginated Material table
│   │   │   ├── confirm-dialog/
│   │   │   ├── loading-overlay/
│   │   │   ├── status-badge/
│   │   │   └── page-header/
│   │   └── pipes/
│   └── features/
│       ├── auth/
│       │   └── login/
│       ├── dashboard/
│       ├── members/
│       │   ├── member-list/
│       │   ├── member-detail/
│       │   └── member-form/
│       ├── families/
│       │   ├── family-list/
│       │   ├── family-detail/
│       │   └── family-form/
│       ├── trainers/
│       │   ├── trainer-list/
│       │   ├── trainer-detail/
│       │   └── trainer-form/
│       ├── programs/
│       │   ├── program-list/
│       │   └── program-form/
│       ├── batches/
│       │   ├── batch-list/
│       │   ├── batch-detail/
│       │   ├── batch-form/
│       │   └── batch-sessions/
│       ├── sessions/
│       │   ├── session-form/
│       │   └── session-attendance/
│       ├── enrollments/
│       │   ├── enrollment-list/
│       │   └── enrollment-form/
│       ├── payments/
│       │   ├── payment-list/
│       │   └── payment-form/
│       ├── groups/
│       │   ├── group-list/
│       │   └── group-detail/
│       ├── notifications/
│       │   ├── notification-list/
│       │   └── send-notification/
│       ├── reports/
│       │   ├── attendance-report/
│       │   ├── payment-report/
│       │   └── enrollment-report/
│       └── audit-logs/
│           └── audit-log-list/
```

---

## Admin Role Access per Feature

| Feature | super_admin | program_manager | accounts | readonly |
|---|---|---|---|---|
| Member management | Full | View + Create | View | View |
| Family management | Full | View + Create | View | View |
| Trainer management | Full | Full | View | View |
| Program management | Full | Full | View | View |
| Batch management | Full | Full | View | View |
| Session scheduling | Full | Full | View | View |
| Attendance management | Full | Full | View | View |
| Payment management | Full | View | Full | View |
| Group management | Full | View | View | View |
| Send notifications | Full | Full | No | No |
| Reports | Full | Attendance | Payment | All (view) |
| Audit logs | Full | No | No | No |

---

## Screens & Features

### Auth
- Login page (username + password)
- Role displayed in header after login

### Dashboard
- Summary stats: total families, active batches, pending payments, unread notifications
- Quick navigation cards

### Member Management
- Paginated list of all member profiles
- Filter by: status, gender, has_login
- Create new member profile
- View member detail: personal info, family memberships, entity relations, login status
- Edit member details
- Create login credentials for a member (admin only)

### Family Management
- Paginated list of all families
- Filter by: status, city
- Create family (generates family_code)
- View family detail: info, address, all family members with roles
- Edit family info and address
- Add/remove family members
- Change member roles

### Trainer Management
- Paginated list of trainers
- Filter by: status, specialization
- Create trainer (links to existing member profile)
- View trainer detail: profile, specialization, bio, assigned batches, address
- Edit trainer details

### Program Management
- List of all programs
- Filter by: status, program_type
- Create/edit program

### Batch Management
- List of all batches
- Filter by: status, program_id, trainer_id
- Create batch (links to program, assigns trainer, sets capacity)
- View batch detail: info, members list, session list
- Edit batch details
- Add members to batch manually

### Session Scheduling
- List sessions for a batch
- Create sessions (single or bulk)
- Edit session: date, time, title, topics, homework, notes, trainer override
- Lock attendance for a completed session
- Change session status

### Attendance Management
- View session attendance grid (members × status)
- Mark/edit attendance per session (if not locked)
- Bulk mark attendance
- View attendance summary per member per batch

### Enrollment Management
- List all enrollments
- Filter by: family, batch, status, payment_status
- Enroll a member into a batch on behalf of a family
- Cancel enrollment
- View enrollment detail with linked payments

### Payment Management
- List all payments
- Filter by: family, payment_type, status, payment_method, date range
- Record a new payment (class_fee, donation, event_fee)
- Record a refund
- View payment detail

### Group Management
- List all groups
- Create/edit group
- View group members
- Add/remove/ban members

### Notifications
- List all notifications (sent to members)
- Send notification to specific members
- Broadcast to batch or group

### Reports

#### Attendance Report
- Select batch + date range
- Table: member name × session dates × status
- Export to CSV

#### Payment Report
- Date range filter
- Summary: total collected, by payment type, by method
- Transaction list
- Export to CSV

#### Enrollment Report
- By program, batch, or date range
- Status breakdown (active, cancelled, completed)
- Payment status breakdown

### Audit Logs
- Paginated list of all activity logs
- Filter by: actor, entity_type, action, date range
- View old_values and new_values JSON diff

---

## Angular Conventions

- All components are standalone
- Angular Material components used throughout
- Use `MatTableDataSource` with `MatPaginator` and `MatSort` for all data tables
- Reusable `DataTableComponent` wraps common table setup
- Forms use Reactive Forms with typed `FormGroup`
- All API model types are in `core/models/`
- Role-based route guards prevent unauthorized navigation
- Use `CanActivateFn` functional guards
- Lazy load all feature modules

---

## Error Handling

Same pattern as Parent App:

| Error | Behavior |
|---|---|
| 401 (expired) | Auto-refresh, retry |
| 401 (refresh failed) | Redirect to admin login |
| 403 | Show snackbar: "You don't have permission to perform this action" |
| 404 | Show "Not found" message in content area |
| 422 | Inline form field errors via `MatFormField` error state |
| 5xx | Show error snackbar with retry |
