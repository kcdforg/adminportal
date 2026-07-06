# Phase 7a — Ionic Parent App

## Context

You are building `kcdf-parents-app` — an Ionic + Angular application for KCDF parents and students.
The `kcdf-api-backend` (Phases 1–6) is already complete and running at `https://api.xyz.com`.

This is a new project from scratch.

---

## Tech Stack

- Ionic Framework (latest)
- Angular (latest stable)
- Standalone components throughout
- Angular Signals for reactive state
- Angular HttpClient
- Capacitor (optional, for future native builds — include config but do not implement native features)

---

## Project Scaffold

```bash
ionic start kcdf-parents-app blank --type=angular --standalone
```

Do not use NgModules. All components must be standalone.

---

## Project Structure

```
kcdf-parents-app/src/app/
├── core/
│   ├── services/
│   │   ├── auth.service.ts
│   │   ├── api.service.ts
│   │   ├── family.service.ts
│   │   ├── member.service.ts
│   │   ├── academics.service.ts
│   │   ├── payment.service.ts
│   │   ├── community.service.ts
│   │   └── notification.service.ts
│   ├── guards/
│   │   ├── auth.guard.ts
│   │   └── role.guard.ts
│   ├── interceptors/
│   │   ├── jwt.interceptor.ts
│   │   └── error.interceptor.ts
│   ├── models/
│   │   ├── auth.model.ts
│   │   ├── member.model.ts
│   │   ├── family.model.ts
│   │   ├── batch.model.ts
│   │   ├── session.model.ts
│   │   ├── attendance.model.ts
│   │   ├── enrollment.model.ts
│   │   ├── payment.model.ts
│   │   ├── group.model.ts
│   │   ├── invitation.model.ts
│   │   └── notification.model.ts
│   └── store/
│       └── auth.store.ts           ← signal-based auth state
├── shared/
│   ├── components/
│   │   ├── loading-spinner/
│   │   ├── empty-state/
│   │   ├── error-banner/
│   │   └── status-badge/
│   └── pipes/
│       ├── date-format.pipe.ts
│       └── currency-inr.pipe.ts
└── features/
    ├── auth/
    │   └── login/
    │       ├── login.page.ts
    │       └── login.page.html
    ├── dashboard/
    │   ├── dashboard.page.ts
    │   └── dashboard.page.html
    ├── family/
    │   ├── family-detail/
    │   ├── add-member/
    │   └── edit-member/
    ├── academics/
    │   ├── enrollment-list/
    │   ├── batch-detail/
    │   ├── session-list/
    │   └── attendance-view/
    ├── payments/
    │   └── payment-history/
    ├── groups/
    │   ├── group-list/
    │   └── group-detail/
    ├── notifications/
    │   └── notification-list/
    └── invitations/
        ├── invite-friend/
        └── accept-invite/
```

---

## Authentication Implementation

### auth.service.ts
- `login(username, password)` → POST /api/v1/auth/login → store tokens
- `logout()` → POST /api/v1/auth/logout → clear tokens
- `refreshToken()` → POST /api/v1/auth/refresh
- `getMe()` → GET /api/v1/auth/me
- Store tokens in localStorage (key: `kcdf_access_token`, `kcdf_refresh_token`)
- Signal: `currentUser = signal<AuthUser | null>(null)`
- Signal: `isAuthenticated = computed(() => currentUser() !== null)`

### jwt.interceptor.ts
- Attach `Authorization: Bearer <token>` to all requests if token exists

### error.interceptor.ts
- On 401: attempt token refresh via auth.service.refreshToken()
- On refresh success: retry original request with new token
- On refresh failure: auth.service.logout(), navigate to /login
- On 403: show toast "Access denied"
- On 5xx: show toast "Server error, please try again"

### auth.guard.ts
- Functional guard using `inject(AuthService)`
- If not authenticated → navigate to /login

### role.guard.ts
- Check roles from currentUser signal
- If role not present → navigate to /dashboard with toast

---

## Screens to Build

### Login Page (`/login`)
- IonInput for username and password
- IonButton to submit
- Show error message on invalid credentials
- Show loading spinner during request
- On success: navigate to /dashboard

### Dashboard Page (`/dashboard`) — protected
- IonCard: enrolled batches count
- IonCard: upcoming sessions this week
- IonCard: unread notifications count
- Quick links to key sections
- IonRefresher for pull-to-refresh

### Family Detail (`/family`)
- Family name and address display
- IonList of family members with relationship and role badges
- FAB button to add member (primary role only)
- Tap member → edit member

### Add / Edit Member
- Form with: first_name, last_name, mobile, email, gender, date_of_birth, blood_group
- Validation inline using Angular Reactive Forms
- Submit → create or update via API

### Enrollment List (`/academics/enrollments`)
- IonList of enrollments for all family members
- Enrollment item: member name, batch name, status badge, payment_status badge
- Tap → Batch Detail

### Batch Detail (`/academics/batches/:id`)
- Batch info: program name, trainer, dates, status
- IonSegment: Sessions | Members
- Sessions tab: list of sessions sorted by date
- Tap session → Session Detail (separate page)

### Session Detail
- Session date, time, title, type, status
- Topics covered, homework, notes (if completed)
- Attendance status for the authenticated user's member(s) in this batch

### Attendance View (`/academics/attendance`)
- Select batch from a dropdown
- Table/list: session date × attendance_status for each family member

### Payment History (`/payments`)
- Filter tabs: All | Class Fees | Donations | Refunds
- IonList of payments sorted by date
- Payment item: amount, type badge, method, date, status badge

### Group List (`/groups`)
- IonList of visible groups
- Join button for public groups (if not already a member)
- Leave button if member
- Tap → Group Detail

### Group Detail
- Group name, description, visibility
- Member list
- Leave group button

### Notification List (`/notifications`)
- IonList, unread items highlighted
- Mark as read on tap
- IonButton: Mark all as read
- Empty state if no notifications

### Invite Friend (`/invitations`)
- Form: mobile number and/or email
- Submit → POST /api/v1/invitations
- List of sent invitations with status badges

### Accept Invite (`/accept/:code`) — public route
- Reads `:code` from route params
- GET /api/v1/invitations/:code — display inviter name and expiry
- Registration form: first_name, last_name, mobile, email, password
- On submit → POST /api/v1/invitations/:code/accept
- On success → save tokens, redirect to /dashboard

---

## Role-Based UI Rules

Implement using a helper computed signal `hasRole(role: string): boolean`.

| Element | Show Condition |
|---|---|
| Add family member FAB | hasRole('family_primary') |
| Enroll member button | hasRole('family_primary') |
| Cancel enrollment button | hasRole('family_primary') |
| Payment history section | hasRole('family_primary') |
| Send invitation section | hasRole('family_primary') or hasRole('family_normal') |
| Join group button | hasRole('family_primary') or hasRole('family_normal') |

---

## Models (TypeScript interfaces)

Define typed interfaces for all API responses:

```typescript
// auth.model.ts
export interface AuthUser {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  roles: string[];
  family_ids: number[];
}

export interface AuthResponse {
  access_token: string;
  refresh_token: string;
  token_type: string;
  expires_in: number;
  profile: AuthUser;
}
```

Define similar interfaces for all other domain models.

---

## Environment

```typescript
// src/environments/environment.ts
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8080/api/v1'
};

// src/environments/environment.prod.ts
export const environment = {
  production: true,
  apiUrl: 'https://api.xyz.com/api/v1'
};
```

---

## Rules

- All components are standalone
- Use `inject()` for DI, not constructor injection
- Use signals for all shared/reactive state
- Use Reactive Forms for all forms with typed FormGroup
- All API calls go through the relevant service (not directly from components)
- Lazy load all feature routes
- Handle loading state per page (IonLoading or skeleton screens)
- Handle empty states with the shared EmptyStateComponent
