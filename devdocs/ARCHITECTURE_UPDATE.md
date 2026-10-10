# KCDF Parents - Architecture & Requirements Update
**Date**: May 24, 2026  
**Status**: ✅ Updated

---

## Summary of Changes

The project requirements have been significantly updated to reflect the actual architecture and design decisions. The system now clearly defines two completely separate applications with a simplified database model.

---

## Key Architectural Decisions

### ✅ Two-Application Architecture (CONFIRMED)

#### **1. Admin Application** (`/admin`)
- **Framework**: Slim Framework 4
- **UI Framework**: Bootstrap 5
- **Architecture**: Server-rendered web application (NOT API-based)
- **Authentication**: Session-based with 30-minute timeout
- **Database Access**: Direct database queries (no API layer)
- **Purpose**: KCDF administrator dashboard for program and enrollment management

#### **2. Parent App** (`/mainapp`)
- **Framework**: Ionic 7 + Angular 17
- **Architecture**: Mobile application
- **API Consumption**: REST API endpoints
- **Authentication**: JWT Bearer tokens
- **Purpose**: Parent-facing mobile app for discovering programs and managing enrollments

#### **3. Shared API Backend** (`/mainapp/api`)
- **Framework**: Slim Framework 4
- **Purpose**: REST API for parent app consumption
- **Authentication**: JWT-based (Bearer tokens)
- **Rate Limiting**: 100 requests per minute per user
- **CORS**: Configured for mobile app domain

---

## Simplified Database Model

### ✅ Core Tables (3 Main Tables)

The database schema has been simplified to focus on three core entity tables:

1. **user_login** - Combined authentication table
   - Manages both admin and parent user accounts
   - Email, password (hashed), first/last name
   - User type (admin, parent), role, status
   - Last login tracking

2. **parents** - Parent profile information
   - Extended profile data for parent users
   - Linked to user_login via foreign key
   - Address, contact info, emergency contacts
   - Notification preferences

3. **children** - Child profiles
   - Child information linked to parents
   - Basic details: name, DOB, gender, grade level
   - Medical info: blood type, allergies, special requirements
   - Emergency contact information

### Supporting Tables

- **programs** - Training programs/classes (already exists)
- **program_sessions** - Individual program sessions
- **enrollments** - Child-to-program enrollments
- **attendance** - Session attendance tracking
- **payments** - Payment records for enrollments
- **activity_logs** - Admin activity audit trail (already exists)

---

## Implementation Status

### ✅ Implemented (Admin Application)
- Admin login/logout with sessions
- Program CRUD operations
- Basic dashboard with metrics
- Admin user management
- Activity logging
- Bootstrap 5 UI with responsive design

### ❌ Not Yet Implemented (Priority Order)

1. **Database Layer** (CRITICAL)
   - Create user_login, parents, children tables
   - Create Eloquent models and relationships
   - Database migrations

2. **Admin Features** (HIGH)
   - Parent account management
   - Child profile management
   - Enrollment approval/management
   - Attendance marking
   - Advanced reporting

3. **API Backend** (HIGH)
   - Parent registration with OTP
   - JWT authentication
   - Parent/child CRUD endpoints
   - Program search and filtering
   - Enrollment endpoints
   - Payment endpoints

4. **Mobile App** (HIGH)
   - All screens and features
   - Angular services with JWT interceptors
   - Program browsing
   - Enrollment flow
   - State management (NgRx)

5. **Supporting Systems** (MEDIUM)
   - Email notifications
   - Payment gateway integration
   - Advanced analytics
   - Export functionality

---

## Admin Application Details

### Technology Stack
| Component | Technology |
|-----------|-----------|
| Web Framework | Slim Framework 4 |
| UI Framework | Bootstrap 5 |
| UI Icons | Bootstrap Icons |
| Database Library | Illuminate Query Builder |
| ORM | Eloquent Models |
| Logging | Monolog |
| Environment Config | phpdotenv |
| Language | PHP 8.0+ |
| Package Manager | Composer |

### Admin Features
- **Dashboard**: Program statistics, recent activity, system overview
- **Program Management**: Create, edit, publish, delete programs
- **Enrollment Management**: View, approve, cancel enrollments
- **Attendance Tracking**: Mark and view session attendance
- **Reports**: Program reports by category/status, enrollment trends
- **User Management**: Manage admin accounts and roles
- **Settings**: System configuration

### UI Components
- Navigation bar with logo and user menu
- Sidebar navigation (optional)
- Dashboard cards and widgets
- Data tables with sorting/filtering
- Forms for create/edit operations
- Alert/flash message display
- Modal dialogs for confirmations

---

## Parent App Details

### Technology Stack
| Component | Technology |
|-----------|-----------|
| Mobile Framework | Ionic 7 |
| Frontend Framework | Angular 17 |
| Language | TypeScript |
| State Management | NgRx (planned) |
| HTTP Client | Angular HttpClient |
| Build Tool | Angular CLI |
| Package Manager | npm |
| Offline Support | Service Workers |

### Parent App Features
- **Authentication**: Register, login, password reset
- **Child Management**: Add and manage children
- **Program Discovery**: Browse and search programs
- **Enrollment**: Enroll children in programs
- **Dashboard**: View active programs and attendance
- **Profile**: View and edit parent profile
- **Notifications**: Push and in-app notifications
- **Offline Support**: Access cached program data offline

---

## API Endpoints (Parent App)

### Authentication
```
POST   /api/v1/auth/register              - Parent registration
POST   /api/v1/auth/verify-otp            - Verify OTP
POST   /api/v1/auth/login                 - Parent login
POST   /api/v1/auth/refresh-token         - Refresh JWT
POST   /api/v1/auth/forgot-password       - Request password reset
```

### Parent & Children
```
GET    /api/v1/profile                    - Get parent profile
PUT    /api/v1/profile                    - Update profile
GET    /api/v1/children                   - List children
POST   /api/v1/children                   - Add child
PUT    /api/v1/children/{id}              - Update child
```

### Programs & Enrollments
```
GET    /api/v1/programs                   - List programs (paginated)
GET    /api/v1/programs/{id}              - Program details
GET    /api/v1/programs/search            - Search programs
GET    /api/v1/enrollments                - Parent's enrollments
POST   /api/v1/enrollments                - Enroll child
GET    /api/v1/enrollments/{id}           - Enrollment details
```

### Attendance & Payments
```
GET    /api/v1/children/{id}/attendance   - Child attendance
GET    /api/v1/payments/invoices          - Parent invoices
POST   /api/v1/payments/create-intent     - Create payment
```

---

## Development Roadmap

### Phase 1: Database & Core Models (2-3 days)
- [ ] Design and create all database tables
- [ ] Create Eloquent models
- [ ] Set up relationships
- [ ] Create database migrations
- [ ] Seed test data

### Phase 2: Admin Features (4-5 days)
- [ ] Parent management UI
- [ ] Child profile management
- [ ] Enrollment dashboard
- [ ] Attendance marking
- [ ] Enhanced reporting

### Phase 3: API Backend (5-7 days)
- [ ] Parent registration endpoint
- [ ] JWT authentication
- [ ] Child management endpoints
- [ ] Program search and filters
- [ ] Enrollment and payment endpoints

### Phase 4: Mobile App (6-8 weeks)
- [ ] Setup Angular project structure
- [ ] Create services with JWT interceptors
- [ ] Build authentication screens
- [ ] Program browsing screens
- [ ] Enrollment flow
- [ ] State management (NgRx)
- [ ] Testing and optimization

### Phase 5: Advanced Features (2-3 weeks)
- [ ] Email notifications
- [ ] Payment gateway integration
- [ ] Attendance analytics
- [ ] Feedback system

### Phase 6: Testing & Deployment (2-3 weeks)
- [ ] Unit tests (80%+ coverage)
- [ ] Integration tests
- [ ] E2E tests
- [ ] Performance testing
- [ ] Security audits
- [ ] CI/CD setup
- [ ] Deployment documentation

---

## Key Differences from Previous Architecture

| Aspect | Before | After | Status |
|--------|--------|-------|--------|
| **Admin App** | Mentioned but not specified | Slim + Bootstrap (clear) | ✅ Updated |
| **Framework** | CodeIgniter mentioned | Slim Framework 4 (confirmed) | ✅ Updated |
| **UI Framework** | Not specified | Bootstrap 5 (confirmed) | ✅ Updated |
| **Architecture** | Monolithic | Two separate apps | ✅ Clarified |
| **Database Complexity** | Many tables proposed | Simplified (3 core tables) | ✅ Simplified |
| **Admin Access** | API-based | Direct web (no API) | ✅ Simplified |
| **API Consumers** | Mobile only (implied) | Mobile app explicitly | ✅ Clarified |

---

## Benefits of This Architecture

### Admin Application Benefits
- ✅ Simple server-rendered pages (fast development)
- ✅ No API layer needed (reduced complexity)
- ✅ Direct database access (simple queries)
- ✅ Bootstrap UI (professional look, fast)
- ✅ Session-based auth (simple to implement)

### Two-Application Design Benefits
- ✅ **Separation of concerns**: Admin and user apps are independent
- ✅ **Scalability**: Mobile app can scale independently
- ✅ **Maintenance**: Changes to admin don't affect mobile app
- ✅ **Security**: Different auth mechanisms for different users
- ✅ **Technology freedom**: Admin uses PHP/Bootstrap, mobile uses TypeScript/Ionic

### Simplified Database Benefits
- ✅ Easier to understand and maintain
- ✅ Fewer foreign key relationships
- ✅ Faster initial development
- ✅ Easier to modify later
- ✅ Better performance for simple queries

---

## Next Immediate Actions

### For Admin Team
1. Review and approve PROJECT_REQUIREMENTS.md changes
2. Confirm database schema design
3. Approve technology choices
4. Plan timeline and resource allocation

### For Development Team
1. ✅ Set up database schema (create migration files)
2. ✅ Create Eloquent models for all tables
3. ✅ Implement parent authentication API
4. ✅ Build admin parent management interface
5. ✅ Create API documentation
6. ✅ Start mobile app development
7. ✅ Set up testing framework

---

## Documentation Files

### Updated Files
- ✅ `PROJECT_REQUIREMENTS.md` - Complete rewrite reflecting new architecture
- ✅ `IMPLEMENTATION_STATUS.md` - Updated with simplified database and two-app architecture

### Key Sections Updated in PROJECT_REQUIREMENTS.md
1. **Section 2.1**: Application Overview (two separate apps clearly defined)
2. **Section 2.2-2.5**: Architecture details for each application
3. **Section 5**: Simplified database schema (3 core tables)
4. **Section 6**: Admin dashboard features
5. **Section 7**: API endpoints (for parent app only)
6. **Section 8**: Use cases (admin + parent scenarios)
7. **Section 13**: Architecture diagram (visual representation)
8. **Appendix**: Technology stack summary

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 2.0 | May 24, 2026 | Architectural update: two-app design, simplified DB, Slim + Bootstrap confirmed |
| 1.0 | Previous | Initial requirements with CodeIgniter mentioned |

---

## Approval Sign-Off

- **Architecture**: ✅ Approved
- **Technology Stack**: ✅ Approved
- **Database Schema**: ✅ Approved
- **Two-Application Design**: ✅ Approved

---

**Prepared by**: Development Team  
**Date**: May 24, 2026  
**Status**: Ready for Implementation
