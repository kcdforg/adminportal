# Parent App — Ionic + Angular

## Overview

The Parent App is built with Ionic + Angular. It is deployed at `parents.xyz.com` and targets both mobile web and can be packaged as a native app via Capacitor.

**Repo:** `kcdf-parents-app`

---

## Tech Stack

- Ionic Framework (latest)
- Angular (latest stable)
- Angular Signals for state management
- Standalone components
- Angular HttpClient with interceptors
- Capacitor (for native packaging, future)

---

## Project Structure

```
kcdf-parents-app/src/
├── app/
│   ├── core/
│   │   ├── services/
│   │   │   ├── auth.service.ts         ← JWT storage, login, logout, token refresh
│   │   │   ├── api.service.ts          ← base HTTP wrapper
│   │   │   └── notification.service.ts
│   │   ├── guards/
│   │   │   ├── auth.guard.ts           ← redirects to login if not authenticated
│   │   │   └── role.guard.ts           ← redirects if role not allowed
│   │   ├── interceptors/
│   │   │   ├── jwt.interceptor.ts      ← attaches Bearer token to all requests
│   │   │   └── error.interceptor.ts    ← handles 401 refresh flow, 403, 5xx
│   │   └── models/
│   │       ├── auth.model.ts
│   │       ├── family.model.ts
│   │       ├── member.model.ts
│   │       ├── batch.model.ts
│   │       ├── session.model.ts
│   │       ├── attendance.model.ts
│   │       ├── enrollment.model.ts
│   │       ├── payment.model.ts
│   │       └── notification.model.ts
│   ├── shared/
│   │   ├── components/
│   │   │   ├── loading-spinner/
│   │   │   ├── empty-state/
│   │   │   ├── error-banner/
│   │   │   └── avatar/
│   │   └── pipes/
│   │       ├── date-format.pipe.ts
│   │       └── currency-format.pipe.ts
│   └── features/
│       ├── auth/
│       │   ├── login/
│       │   └── forgot-password/
│       ├── dashboard/
│       ├── family/
│       │   ├── family-detail/
│       │   ├── add-member/
│       │   └── edit-member/
│       ├── academics/
│       │   ├── enrollments/
│       │   ├── batch-detail/
│       │   ├── session-list/
│       │   └── attendance/
│       ├── payments/
│       │   └── payment-history/
│       ├── groups/
│       │   ├── group-list/
│       │   └── group-detail/
│       ├── notifications/
│       │   └── notification-list/
│       └── invitations/
│           └── invite-friend/
```

---

## Authentication Flow

1. User opens app → `AuthGuard` checks for valid JWT
2. No token → redirect to `/login`
3. Login form → `POST /api/v1/auth/login`
4. On success → store access token + refresh token in secure storage
5. `JwtInterceptor` attaches `Authorization: Bearer <token>` to all requests
6. On 401 response → `ErrorInterceptor` attempts silent refresh via `POST /api/v1/auth/refresh`
7. If refresh succeeds → retry original request with new token
8. If refresh fails → clear tokens, redirect to `/login`
9. Logout → `POST /api/v1/auth/logout` → clear tokens → redirect to `/login`

---

## Screens & Features

### Auth
- Login screen (username + password)
- Error display for invalid credentials or deactivated account

### Dashboard
- Welcome message with profile name
- Summary cards: enrolled batches, upcoming sessions, unread notifications
- Quick links to key sections

### Family Management
- View family name and address
- List all family members with role and relationship type
- Add a new member (primary only)
- Edit member profile details
- View member photo, blood group, contact info

### Academics

#### Enrollments
- List of enrollments for family members
- Enrollment status and payment status badges
- Enroll a student in a batch (primary only)
- Cancel enrollment (primary only)

#### Batch Detail
- Batch info: name, program, trainer, dates
- List of sessions with status and date
- Member count

#### Session List
- Filter sessions by date range and status
- Session card: date, time, title, status, session type
- Tap session to view topics covered, homework, notes

#### Attendance
- View attendance for a specific session (own members only)
- Attendance status badge: present/absent/late/excused
- Overall attendance summary per member per batch

### Payments / Donations
- List of all payments for the family
- Filter by payment_type (class_fee, donation, event_fee, refund)
- Payment detail: amount, method, date, reference, status

### Parent Groups
- List of groups (public visible to all, private/invite_only only if member)
- Join a public group
- Leave a group
- View group members

### Notifications
- List of notifications sorted by newest first
- Unread count badge
- Mark as read on open
- Mark all as read
- Archive notifications

### Invitations
- Send an invitation via mobile number or email
- View list of sent invitations and their status
- Invitation accept screen (accessed via invite link/code)

---

## Role-Based UI Rules

| Feature | family_primary | family_normal | family_student |
|---|---|---|---|
| View family details | Yes | Yes | No |
| Add/edit family members | Yes | No | No |
| Enroll members | Yes | No | No |
| Cancel enrollment | Yes | No | No |
| View own attendance | Yes | Yes | Yes (own only) |
| View payments | Yes | No | No |
| Join public groups | Yes | Yes | No |
| Send invitations | Yes | Yes | No |

---

## Angular Conventions

- All components are standalone
- Use `inject()` for dependency injection, not constructor injection
- Use signals for local component state
- API calls go through feature-specific services that extend `ApiService`
- All API response types are modelled in `core/models/`
- Error handling is centralized in `ErrorInterceptor` — individual components only handle business-level errors
- Route guards use `CanActivateFn` functional guards
- Lazy load all feature modules

---

## Error Handling

| Error | Behavior |
|---|---|
| 401 (token expired) | Auto-refresh silently, retry request |
| 401 (refresh failed) | Redirect to login |
| 403 | Show "Access denied" toast |
| 404 | Show empty state component |
| 422 | Display field-level errors inline on form |
| 5xx | Show generic error banner with retry option |
| Network error | Show offline toast |
