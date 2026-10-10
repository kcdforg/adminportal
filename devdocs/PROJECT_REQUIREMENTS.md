# KCDF Parents - Educational Program Registration System
## Professional Product Requirements Document

---

## **1. Executive Summary**

### **Project Vision**
KCDF Parents is a comprehensive digital platform comprising two separate applications:
1. **Admin Application** - A web-based management console for KCDF administrators to manage programs, enrollments, and participants
2. **Parent App** - A mobile application for parents to discover programs, register children, and manage enrollments

The system bridges the gap between parents seeking quality educational opportunities and the organization's need for efficient program management.

### **Business Objectives**
- Enable seamless parent registration and profile management
- Digitize the child enrollment process for educational programs
- Reduce administrative overhead through automation
- Improve communication between the organization and parents
- Provide data-driven insights for program planning and optimization
- Scale operations to serve more families efficiently

### **Target Users**
- **Primary**: Parents/guardians seeking educational programs for their children
- **Secondary**: KCDF administrators and program coordinators
- **Tertiary**: Finance team for enrollment tracking and reporting

### **Success Criteria**
- 500+ parent registrations within first 6 months
- 90% reduction in manual registration processing time
- 95% system uptime and availability
- <2 second average page load time
- 4.5+ user satisfaction rating

---

## **2. System Architecture & Technical Stack**

### **2.1 Application Overview**

The KCDF Parents system consists of **two completely separate applications**:

#### **Admin Application** (Backend Management)
- Located in `/admin` directory
- Slim Framework 4 web application
- Server-rendered HTML views with Bootstrap 5 UI
- Session-based authentication
- Direct database access (no API layer)
- Dashboard for program and enrollment management

#### **Parent App** (User-Facing Mobile)
- Located in `/mainapp` directory
- Ionic 7 + Angular 17 mobile application
- REST API consumption (API endpoints provided by backend)
- Cross-platform (iOS & Android)
- Modern mobile UX with offline support

---

### **2.2 Admin Application Architecture**

#### **Primary Framework: Slim Framework 4**
- **Language**: PHP 8.0+ (PHP 8.1+ recommended)
- **Application Type**: Server-rendered web application (NOT API)
- **UI Framework**: Bootstrap 5 with Bootstrap Icons
- **Authentication**: Session-based with timeout (30 minutes inactivity)
- **Template Engine**: PHP View (Slim/PHP-View)
- **Database Abstraction**: Illuminate Query Builder with Eloquent models
- **Routing**: Slim's PSR-15 routing middleware
- **Middleware Stack**: 
  - Authentication middleware (session validation)
  - Flash message middleware
  - Body parsing middleware
  - Error handling middleware

#### **Admin Database**
- **Primary DB**: MySQL 5.7+ or MariaDB 10.3+
- **Simple Schema**: Minimal tables for efficient management
- **Shared Connection**: Single database connection with mainapp API backend
- **Backup Strategy**: Daily automated backups with 30-day retention
- **Compatibility**: Standard shared hosting support (cPanel/Plesk)

#### **API Documentation**
- **Tool**: OpenAPI/Swagger or Postman Collection
- **Documentation**: Auto-generated from code annotations or manual
- **Accessibility**: `/api/docs` endpoint or exported Postman collection
- **Framework Documentation**: Available at https://www.slimframework.com

#### **Email & Communication**
- **Primary**: PHPMailer with SMTP (via hosting provider)
- **Alternative**: Mailgun API integration
- **Queue**: Simple database-based job queue for bulk communications
- **Configuration**: Via hosting control panel's email settings

#### **File Storage**
- **Local**: `/uploads` directory for program materials and documents
- **Production**: Shared hosting file system or optional S3 integration
- **Backup Documents**: Parent verification documents, enrollment receipts
- **Program Materials**: Images, schedules, instructional materials

### **2.3 Parent App (Mobile) Architecture**

#### **Mobile Framework: Ionic 7 with Angular 17**
- **Language**: TypeScript
- **Package Manager**: npm
- **Build Tool**: Angular CLI
- **API Communication**: REST API with JWT Bearer tokens
- **HTTP Client**: Angular HttpClient with JWT interceptors
- **Authentication**: Token-based (JWT from API backend)
- **State Management**: NgRx for complex state scenarios
- **UI Components**: Ionic pre-built components
- **Forms**: Reactive Forms with custom validators
- **Offline Support**: Service Workers for caching

#### **Mobile Responsive Design**
- **Breakpoints**: Mobile (360px), Tablet (768px), Desktop (1024px+)
- **Mobile First**: Progressive enhancement approach
- **Accessibility**: WCAG 2.1 AA compliance
- **Platform Support**:
  - iOS 14+
  - Android 9+

#### **Performance Optimization**
- **Code Splitting**: Lazy loading for feature modules
- **Compression**: Gzip compression for assets
- **Image Optimization**: WebP format with fallbacks
- **Caching**: Service workers and HTTP caching
- **Progressive Web App**: Offline-first capabilities

### **2.4 Backend API Architecture**

#### **API Application** (Located in `/mainapp/api`)
- **Framework**: Slim Framework 4
- **Purpose**: Provide REST API endpoints for Parent App mobile consumption
- **Authentication**: JWT Bearer tokens (24-hour expiry, 30-day refresh tokens)
- **Response Format**: JSON exclusively
- **Rate Limiting**: 100 requests per minute per user
- **CORS**: Configured for mobile app domain

#### **Backend API Features**
- Parent registration and authentication
- Parent profile management
- Child profile management
- Program browsing and search
- Enrollment management
- Payment processing API calls
- Attendance data retrieval
- Notification preferences

### **2.5 Development Environment**

#### **Admin Application Setup**
- Local PHP 8.0+ with built-in server
- Local MySQL database
- Composer for dependency management
- VS Code or text editor

#### **Parent App Development**
- Node.js 16+ with npm
- Angular CLI
- Ionic CLI
- iOS Simulator or Android Emulator

#### **Staging Environment**
- Separate shared hosting account (optional)
- Or dedicated subdomain on production
- Mirrors production configuration

#### **Production Environment**
- **Hosting Options**:
  - Shared Hosting: cPanel/Plesk-based (recommended for simplicity)
  - VPS: DigitalOcean or Linode (optional for better performance)
- **SSL/TLS**: Let's Encrypt with auto-renewal
- **Server Configuration**: Apache with .htaccess or Nginx
- **Monitoring**: Basic monitoring via hosting control panel

#### **Deployment Process - Admin App**
- FTP/SFTP upload or Git pull via SSH
- Database: Create via phpMyAdmin
- Configuration: Update `.env` file
- PHP Requirements: 8.0+ with curl, json, pdo, pdo_mysql
- Webroot: Point domain to `/admin/public`

#### **Deployment Process - Parent App**
- Build: `npm run build` generates optimized bundle
- Deploy to: App Store (iOS) and Google Play Store (Android)
- Or: Deploy as PWA to web hosting for browser access
- Configuration: API endpoint configuration per environment

---

## **3. Functional Requirements**

### **3.1 Authentication & Authorization**

#### **Admin Authentication**
- **Email and password login** via web form
- **Session management** with timeout (30 minutes of inactivity)
- **Activity logging** for security audit trail
- **Admin roles**: Program Coordinator, Finance Admin, System Admin, Super Admin

#### **Parent Authentication (API)**
- **Email-based registration** with OTP verification
- **Password strength** requirements (minimum 8 characters, uppercase, numbers, special chars)
- **"Remember me" functionality** (14-day token expiry)
- **Password reset** via email link (valid for 1 hour)
- **Mobile API tokens**: Bearer JWT tokens, 24-hour expiry with 30-day refresh tokens
- **Rate limiting**: 100 requests per minute per user
- **CORS**: Configured for mobile app domain

#### **Authorization & Admin Roles**
| Role | Permissions |
|------|------------|
| **Program Coordinator** | Create/edit programs, view enrollments, mark attendance |
| **Finance Admin** | View payments, generate financial reports, export data |
| **System Admin** | Full system access, user management, system configuration |
| **Super Admin** | All permissions including admin staff management |

### **3.2 Parent Management Module**

#### **Parent Registration Flow**
1. User enters email address
2. System sends OTP to email
3. User verifies OTP
4. User creates password
5. User completes profile information
6. Account activated and ready to use

#### **Parent Profile Information**
- **Required Fields**:
  - Full name (first, middle, last)
  - Email address (unique, verified)
  - Phone number (with country code)
  - National ID or Passport number
  - Physical address (street, city, postal code)
  - Country

- **Optional Fields**:
  - Alternate contact phone
  - Employer name
  - Occupation
  - Emergency contact details
  - Preferred communication method

#### **Profile Management Features**
- Edit profile information
- Update password
- Manage notification preferences
- View account activity log
- Download data export (GDPR compliance)
- Deactivate/delete account (with 30-day grace period)

#### **Privacy & Data Protection**
- SSL/TLS encryption for all data in transit
- AES-256 encryption for sensitive fields at rest
- PII masking in logs and reports
- GDPR/CCPA compliance features
- Data retention policy: 5 years post-account deletion

### **3.3 Child Registration Module**

#### **Child Profile Information**
- **Required Fields**:
  - Full name (first, middle, last)
  - Date of birth
  - Gender
  - Grade level/class
  - School name

- **Optional Fields**:
  - Blood type
  - Allergies/medical conditions
  - Special requirements or disabilities
  - Emergency contact (can differ from parent)
  - Medical information

#### **Child Management Features**
- Add multiple children to parent account
- Edit child information
- Upload child photo (optional, for identification)
- Set educational interests/preferences
- View all registered programs
- Manage child account permissions (who can manage this child)

#### **Child Safety Features**
- Relationship verification (parent-child link verification)
- Parental consent for data processing
- Child data protection compliance (COPPA-like approach)
- Emergency contact information validation

### **3.4 Program Management Module**

#### **Program Creation (Admin)**
- **Program Details**:
  - Program name and unique code
  - Category (Academic, Sports, Arts, Technology, etc.)
  - Description and objectives (rich text editor)
  - Target age range and grade levels
  - Program duration (start date, end date)
  - Session schedule (weekly, bi-weekly, monthly patterns)

- **Program Logistics**:
  - Location/venue with GPS coordinates
  - Capacity (max participants)
  - Instructor/facilitator information
  - Equipment or material requirements
  - Cost (free, fixed fee, or sliding scale)
  - Payment terms and methods

- **Program Status Management**:
  - Draft → Approved → Published → Active → Completed → Archived
  - Pause/resume functionality
  - Cancellation with parent notification

#### **Program Visibility**
- Public programs (visible to all parents)
- Private programs (invitation-only or restricted)
- Seasonal programs with auto-archival
- Featured programs on dashboard

#### **Program Dashboard**
- Enrollment statistics (enrolled, waitlisted, cancelled)
- Attendance tracking per session
- Revenue tracking (if paid program)
- Parent feedback and ratings
- Program performance analytics

### **3.5 Registration & Enrollment System**

#### **Program Discovery**
- Browse all active programs with filters:
  - Category, age group, location, schedule
  - Free vs paid programs
  - Availability status
- Search functionality with autocomplete
- Program detail page with full information
- Instructor profiles and qualifications
- Parent reviews and ratings

#### **Enrollment Process**
1. Parent selects child for enrollment
2. Confirm child age eligibility
3. Accept terms and conditions
4. Review program details
5. Process payment (if applicable)
6. Receive confirmation email
7. Add to calendar (ICS file download)

#### **Payment Processing**
- **Providers**: Stripe, PayPal, M-Pesa (for Kenya)
- **Security**: PCI-DSS compliant payment gateway
- **Invoice Generation**: Automatic PDF invoice creation
- **Payment Plans**: Support for installment options
- **Refund Policy**: Configurable cancellation/refund windows

#### **Enrollment Management**
- View all enrollments (active, past, cancelled)
- Waitlist for full programs (auto-enrollment if spot opens)
- Cancellation with configurable grace period
- Enrollment history and receipts
- Print enrollment confirmation

#### **Attendance Tracking**
- Mark attendance per session
- Generate attendance reports for parents
- Track participation streaks
- Progress badges and achievements

### **3.6 Communication System**

#### **Email Notifications**
| Event | Recipients | Content |
|-------|-----------|---------|
| Registration Confirmation | Parent | Welcome, program details, access info |
| Program Reminder | Enrolled Parents | 24-hour reminder before program |
| Attendance Update | Parent | Attendance summary for session |
| Program Cancellation | Enrolled Parents | Cancellation reason, refund info |
| Password Reset | User | Reset link (1-hour expiry) |
| New Program Available | Interested Parents | Program details and enrollment link |
| Payment Receipt | Parent | Invoice with program details |
| Announcement | All/Selected Parents | General updates and news |

#### **Communication Preferences**
- Opt-in/opt-out for each notification type
- Preferred contact method (email, SMS, both)
- Frequency settings (immediate, daily digest, weekly)
- Language preferences (English, Swahili, etc.)

#### **Notification Management**
- In-app notification center
- Email notification aggregation
- SMS for critical updates
- Push notifications for mobile app

### **3.7 Admin Dashboard & Reporting**

#### **Dashboard Overview**
- Key metrics (total parents, children, programs, enrollments)
- Recent registrations and enrollments
- Upcoming programs
- Revenue summary (if applicable)
- System health status

#### **Reports & Analytics**
- **Parent Reports**:
  - New parent registrations (daily, weekly, monthly)
  - Parent demographics analysis
  - Parent retention and churn rates
  - Parent feedback and satisfaction

- **Program Reports**:
  - Enrollment by program
  - Program occupancy rates
  - Waitlist analytics
  - Program performance trends
  - Revenue by program (if paid)

- **Financial Reports** (Admin):
  - Total revenue by period
  - Payment breakdown by method
  - Outstanding payment tracking
  - Refund tracking

- **Export Options**:
  - CSV export for data analysis
  - PDF reports for sharing
  - Scheduled report emails
  - API access for BI tools

#### **Data Visualization**
- Charts and graphs (bar, line, pie)
- Filterable by date range, category, location
- Real-time dashboard updates
- Downloadable charts

#### **User Management** (Super Admin)
- View all users and roles
- Activate/deactivate accounts
- Assign/modify roles and permissions
- View user activity logs
- Reset passwords for users

### **3.8 Mobile App Features**

#### **iOS & Android Features**
- Native push notifications
- Offline mode for program browsing
- Biometric authentication (fingerprint/face ID)
- Home screen shortcuts
- Deep linking from email/web
- App icon badge with pending notifications

#### **App-Specific Features**
- Share programs with other parents
- Add programs to device calendar
- In-app video player for program content
- Native camera integration for profile photos
- Offline access to enrollment confirmations

---

## **4. Non-Functional Requirements**

### **4.1 Performance**
- **Page Load Time**: < 2 seconds (85th percentile)
- **API Response Time**: < 500ms (95th percentile)
- **Database Query Time**: < 100ms (average)
- **Mobile App Launch Time**: < 3 seconds
- **Concurrent Users**: Support for 1,000+ simultaneous users
- **Database Size**: Support for 1M+ records with optimal performance

### **4.2 Scalability**
- Horizontal scaling capability through load balancing
- Auto-scaling based on traffic patterns
- Database read replicas for reporting
- CDN for static asset distribution
- Queue system for background jobs
- Caching strategies (Redis, browser cache)

### **4.3 Reliability & Availability**
- **SLA**: 99.5% uptime guarantee
- **Redundancy**: Multi-region deployment ready
- **Disaster Recovery**: RTO 1 hour, RPO 15 minutes
- **Automated Failover**: Database replication and failover
- **Health Monitoring**: Real-time system health checks
- **Incident Response**: 24/7 monitoring with alerting

### **4.4 Security**
- **Data Protection**:
  - AES-256 encryption at rest
  - TLS 1.3 for data in transit
  - Secure password hashing (bcrypt, Argon2)
  
- **Access Control**:
  - Role-based access control (RBAC)
  - Session management and timeout
  - IP whitelisting for admin access
  - VPN for internal access
  
- **Application Security**:
  - Protection against OWASP Top 10 vulnerabilities
  - SQL injection prevention (parameterized queries)
  - XSS and CSRF protection
  - Rate limiting and DDoS protection
  - Regular security audits and penetration testing
  
- **Compliance**:
  - GDPR compliance
  - Data residency requirements
  - Audit logging of all sensitive operations
  - Regular security training for team

### **4.5 Maintainability**
- Code follows PSR-12 standards (PHP) and PSR-4 autoloading
- Angular style guide compliance (frontend)
- Comprehensive code documentation
- Slim Framework middleware pattern for separation of concerns
- Illuminate database components follow Laravel conventions
- Unit test coverage: 80%+ for business logic
- Integration test coverage: 60%+ for API endpoints
- Automated code quality checks (SonarQube, PHPStan)
- Version control with Git and semantic versioning

### **4.6 Usability**
- Intuitive and clean user interface
- Mobile-first responsive design
- Accessibility compliance (WCAG 2.1 AA)
- Multi-language support (English, Swahili)
- Consistent branding and design system
- User onboarding tutorials
- Contextual help and tooltips
- Error messages clear and actionable

### **4.7 Browser & Device Support**
- **Web Browsers**:
  - Chrome 90+
  - Firefox 88+
  - Safari 14+
  - Edge 90+
  
- **Mobile Devices**:
  - iOS 14+
  - Android 9+
  - Screen sizes: 320px to 2560px width

---

## **5. Data Model & Database Schema**

### **5.1 Core Database Tables**

The database uses a simplified, efficient schema with three core tables and supporting tables for programs and enrollments.

#### **user_login Table** (Admin & Parent users)
```
user_login
├── id (BIGINT, PK, Auto-increment)
├── email (VARCHAR 255, UNIQUE, NOT NULL)
├── password (VARCHAR 255, NOT NULL, bcrypt hashed)
├── first_name (VARCHAR 100, NOT NULL)
├── last_name (VARCHAR 100, NOT NULL)
├── phone (VARCHAR 20, NULLABLE)
├── national_id (VARCHAR 50, UNIQUE, NULLABLE)
├── user_type (ENUM: 'admin', 'parent', default: 'parent')
├── role (VARCHAR 50, default: 'coordinator' for admin, 'parent' for users)
├── is_active (TINYINT(1), default: 1)
├── last_login_at (DATETIME, NULLABLE)
├── created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
├── updated_at (TIMESTAMP, ON UPDATE CURRENT_TIMESTAMP)
├── deleted_at (DATETIME, NULLABLE, for soft delete)
```

#### **parents Table** (Parent profile information)
```
parents
├── id (BIGINT, PK, Auto-increment)
├── user_login_id (BIGINT, FK → user_login.id, UNIQUE)
├── address (VARCHAR 255, NULLABLE)
├── city (VARCHAR 100, NULLABLE)
├── postal_code (VARCHAR 20, NULLABLE)
├── country (VARCHAR 100, NULLABLE)
├── employer (VARCHAR 255, NULLABLE)
├── occupation (VARCHAR 100, NULLABLE)
├── emergency_contact_name (VARCHAR 100, NULLABLE)
├── emergency_contact_phone (VARCHAR 20, NULLABLE)
├── profile_photo_url (VARCHAR 255, NULLABLE)
├── notification_preferences (JSON, NULLABLE)
├── created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
├── updated_at (TIMESTAMP, ON UPDATE CURRENT_TIMESTAMP)
├── deleted_at (DATETIME, NULLABLE)
```

#### **children Table** (Child profiles)
```
children
├── id (BIGINT, PK, Auto-increment)
├── parent_id (BIGINT, FK → parents.id, NOT NULL)
├── first_name (VARCHAR 100, NOT NULL)
├── last_name (VARCHAR 100, NOT NULL)
├── date_of_birth (DATE, NOT NULL)
├── gender (ENUM: 'male', 'female', 'other', NULLABLE)
├── grade_level (VARCHAR 50, NULLABLE)
├── school_name (VARCHAR 255, NULLABLE)
├── blood_type (VARCHAR 10, NULLABLE)
├── allergies (TEXT, NULLABLE)
├── special_requirements (TEXT, NULLABLE)
├── emergency_contact_name (VARCHAR 100, NULLABLE)
├── emergency_contact_phone (VARCHAR 20, NULLABLE)
├── photo_url (VARCHAR 255, NULLABLE)
├── created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
├── updated_at (TIMESTAMP, ON UPDATE CURRENT_TIMESTAMP)
├── deleted_at (DATETIME, NULLABLE)
```

#### **programs Table** (Training programs/classes)
```
programs
├── id (BIGINT, PK, Auto-increment)
├── name (VARCHAR 255, NOT NULL, UNIQUE)
├── slug (VARCHAR 255, UNIQUE, NOT NULL)
├── code (VARCHAR 50, UNIQUE, NOT NULL)
├── category (VARCHAR 100, default: 'Academic')
├── description (TEXT, NULLABLE)
├── start_date (DATE, NULLABLE)
├── end_date (DATE, NULLABLE)
├── venue (VARCHAR 255, NULLABLE)
├── max_capacity (INT, default: 30)
├── current_enrollment (INT, default: 0)
├── price (DECIMAL(10,2), default: 0.00)
├── status (ENUM: 'draft', 'published', 'active', 'completed', 'archived', default: 'draft')
├── is_public (TINYINT(1), default: 1)
├── target_age_min (INT, NULLABLE)
├── target_age_max (INT, NULLABLE)
├── grade_levels (VARCHAR 255, NULLABLE)
├── instructor_name (VARCHAR 255, NULLABLE)
├── schedule_details (TEXT, NULLABLE)
├── requirements (TEXT, NULLABLE)
├── published_at (DATETIME, NULLABLE)
├── created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
├── updated_at (TIMESTAMP, ON UPDATE CURRENT_TIMESTAMP)
├── deleted_at (DATETIME, NULLABLE)
```

#### **enrollments Table** (Program enrollments)
```
enrollments
├── id (BIGINT, PK, Auto-increment)
├── child_id (BIGINT, FK → children.id, NOT NULL)
├── program_id (BIGINT, FK → programs.id, NOT NULL)
├── parent_id (BIGINT, FK → parents.id, NOT NULL)
├── enrollment_date (DATE, NOT NULL)
├── status (ENUM: 'active', 'completed', 'cancelled', 'waitlisted', default: 'active')
├── payment_status (ENUM: 'pending', 'completed', 'refunded', default: 'pending')
├── amount_paid (DECIMAL(10,2), default: 0.00)
├── attended_sessions (INT, default: 0)
├── total_sessions (INT, default: 0)
├── cancellation_reason (TEXT, NULLABLE)
├── cancellation_date (DATE, NULLABLE)
├── feedback_rating (INT(1), NULLABLE, range 1-5)
├── feedback_comment (TEXT, NULLABLE)
├── created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
├── updated_at (TIMESTAMP, ON UPDATE CURRENT_TIMESTAMP)
├── deleted_at (DATETIME, NULLABLE)
```

#### **program_sessions Table** (Individual program sessions)
```
program_sessions
├── id (BIGINT, PK, Auto-increment)
├── program_id (BIGINT, FK → programs.id, NOT NULL)
├── session_number (INT, NOT NULL)
├── session_date (DATE, NOT NULL)
├── start_time (TIME, NOT NULL)
├── end_time (TIME, NOT NULL)
├── venue (VARCHAR 255, NULLABLE)
├── instructor_notes (TEXT, NULLABLE)
├── created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
├── updated_at (TIMESTAMP, ON UPDATE CURRENT_TIMESTAMP)
```

#### **attendance Table** (Session attendance tracking)
```
attendance
├── id (BIGINT, PK, Auto-increment)
├── program_session_id (BIGINT, FK → program_sessions.id, NOT NULL)
├── enrollment_id (BIGINT, FK → enrollments.id, NOT NULL)
├── attended (TINYINT(1), default: 0)
├── marked_at (DATETIME, NOT NULL)
├── marked_by_id (BIGINT, FK → user_login.id, NULLABLE)
├── notes (TEXT, NULLABLE)
├── created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
```

#### **payments Table** (Payment records)
```
payments
├── id (BIGINT, PK, Auto-increment)
├── enrollment_id (BIGINT, FK → enrollments.id, NOT NULL, UNIQUE)
├── amount (DECIMAL(10,2), NOT NULL)
├── currency (VARCHAR 3, default: 'KES')
├── payment_method (ENUM: 'card', 'mpesa', 'bank_transfer', NULLABLE)
├── transaction_id (VARCHAR 255, NULLABLE, UNIQUE)
├── status (ENUM: 'pending', 'completed', 'failed', 'refunded', default: 'pending')
├── payment_gateway_response (JSON, NULLABLE)
├── paid_at (DATETIME, NULLABLE)
├── created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
├── updated_at (TIMESTAMP, ON UPDATE CURRENT_TIMESTAMP)
```

#### **activity_logs Table** (Admin activity audit trail)
```
activity_logs
├── id (BIGINT, PK, Auto-increment)
├── admin_user_id (BIGINT, FK → user_login.id, NULLABLE)
├── action (VARCHAR 100, NOT NULL)
├── details (TEXT, NULLABLE)
├── ip_address (VARCHAR 45, NULLABLE)
├── created_at (DATETIME, NOT NULL)
```

---

## **6. Admin Application Features**

### **6.1 Admin Dashboard**
- **Key Metrics**:
  - Total programs (active, draft, completed)
  - Total enrollments (active, completed, cancelled)
  - Enrollment trends (weekly, monthly)
  - Revenue summary (if paid programs)
  - Recent activity log
  
- **Quick Actions**:
  - Create new program
  - View pending enrollments
  - Mark attendance
  - View reports

### **6.2 Program Management**
- **Create Program**:
  - Program name, code, category
  - Description and objectives
  - Target age and grade levels
  - Start/end dates and schedule
  - Venue and capacity
  - Cost and payment terms
  - Status management (draft → published → active → completed/archived)

- **Edit Program**:
  - Update program details
  - Modify capacity and pricing
  - Publish/archive programs
  - View enrollment status

- **Program List**:
  - Filter by status, category, date
  - Quick actions (edit, delete, publish)
  - Search by name or code

### **6.3 Enrollment Management**
- **View Enrollments**:
  - List enrollments with filters (status, program, child, date)
  - Enrollment details and history
  - Payment status
  - Attendance summary
  - Feedback/ratings

- **Manage Enrollments**:
  - Approve/reject enrollments
  - Update enrollment status
  - Cancel enrollments (with notifications)
  - Process refunds

### **6.4 Attendance Tracking**
- **Mark Attendance**:
  - Select program session
  - Mark children as present/absent
  - Add attendance notes
  - Bulk operations

- **Attendance Reports**:
  - Per program attendance
  - Per child attendance
  - Attendance trends
  - Export to CSV

### **6.5 Reports & Analytics**
- **Program Reports**:
  - Programs by category
  - Programs by status
  - Enrollment by program
  - Program occupancy rates

- **Enrollment Reports**:
  - New enrollments (by date, program, child)
  - Enrollment trends
  - Cancellations and refunds
  - Revenue by program

- **User Reports**:
  - Parent registrations
  - Active parents
  - Children registered
  - User activity

- **Export Options**:
  - CSV format for Excel/analysis
  - PDF reports for printing/sharing
  - Scheduled email reports

### **6.6 User Management** (Super Admin)
- **List Admin Users**:
  - View all admin accounts
  - Filter by role, status
  - View last login, activity

- **Create/Edit Admin**:
  - Add new admin users
  - Assign roles
  - Activate/deactivate accounts
  - Reset passwords

### **6.7 Settings & Configuration**
- **System Settings**:
  - Application name, logo
  - Email configuration
  - Payment settings
  - Backup scheduling

---

## **7. API Endpoints Specification** (For Parent App)

### **7.1 Authentication Endpoints**
```
POST   /api/v1/auth/register              Register new parent
POST   /api/v1/auth/verify-otp            Verify email OTP
POST   /api/v1/auth/login                 Parent login with JWT token
POST   /api/v1/auth/logout                Logout and invalidate token
POST   /api/v1/auth/refresh-token         Refresh access token
POST   /api/v1/auth/forgot-password       Request password reset
POST   /api/v1/auth/reset-password        Reset password with token
```

### **7.2 Parent Profile Endpoints**
```
GET    /api/v1/profile                    Get current parent profile
PUT    /api/v1/profile                    Update profile
POST   /api/v1/profile/change-password    Change password
GET    /api/v1/profile/activity-log       Get parent activity log
DELETE /api/v1/profile                    Delete account (30-day grace)
```

### **7.3 Children Endpoints**
```
GET    /api/v1/children                   List parent's children
POST   /api/v1/children                   Add new child
GET    /api/v1/children/{childId}         Get child details
PUT    /api/v1/children/{childId}         Update child
DELETE /api/v1/children/{childId}         Delete child
POST   /api/v1/children/{childId}/photo   Upload child photo
```

### **7.4 Programs Endpoints**
```
GET    /api/v1/programs                   List all programs (paginated, filterable)
GET    /api/v1/programs/{programId}       Get program details
GET    /api/v1/programs/search            Search programs by name/category
GET    /api/v1/programs/{programId}/sessions    Get program sessions
GET    /api/v1/programs/{programId}/reviews     Get program reviews/feedback
```

### **7.5 Enrollment Endpoints**
```
GET    /api/v1/enrollments                List parent's enrollments
POST   /api/v1/enrollments                Enroll child in program
GET    /api/v1/enrollments/{enrollmentId} Get enrollment details
PUT    /api/v1/enrollments/{enrollmentId} Update enrollment
DELETE /api/v1/enrollments/{enrollmentId} Cancel enrollment
POST   /api/v1/enrollments/{enrollmentId}/feedback    Submit feedback
GET    /api/v1/programs/{programId}/waitlist    Get waitlist info
```

### **7.6 Attendance Endpoints** (Parent view)
```
GET    /api/v1/children/{childId}/attendance    Get child attendance summary
GET    /api/v1/enrollments/{enrollmentId}/attendance    Get enrollment attendance
```

### **7.7 Payments Endpoints**
```
POST   /api/v1/payments/create-intent     Create payment intent
POST   /api/v1/payments/confirm           Confirm payment
GET    /api/v1/payments/invoices          Get parent invoices
GET    /api/v1/payments/invoices/{id}     Download invoice PDF
```

---

## **8. User Stories & Use Cases**

### **Use Case 1: Admin Creates a Program**
**Actor**: Program Coordinator (Admin)
**Flow**:
1. Admin logs into admin dashboard
2. Navigates to Programs → Create Program
3. Fills in program details (name, category, dates, venue, capacity, price)
4. Sets schedule and sessions
5. Publishes program
6. Program becomes visible to parents via mobile app

**Acceptance Criteria**:
- Program appears in parent app
- Sessions are properly scheduled
- Capacity tracking works
- Admin can edit/delete

### **Use Case 2: Parent Discovers and Enrolls Child**
**Actor**: Parent (Mobile App)
**Prerequisites**: Parent registered and logged in
**Flow**:
1. Parent opens mobile app
2. Browses programs (filter by age, category)
3. Selects a program
4. Views program details and schedule
5. Clicks "Enroll Child"
6. Selects which child to enroll
7. Reviews terms and conditions
8. Completes payment (if required)
9. Receives confirmation

**Acceptance Criteria**:
- Enrollment saved in database
- Confirmation email sent
- Enrollment visible in parent's dashboard
- Payment processed (if applicable)

### **Use Case 3: Admin Marks Attendance**
**Actor**: Program Coordinator (Admin)
**Flow**:
1. Admin logs into admin dashboard
2. Navigates to Attendance
3. Selects program and session date
4. Marks children as present/absent
5. Adds notes if needed
6. Submits attendance
7. Parent receives attendance notification

**Acceptance Criteria**:
- Attendance recorded in database
- Attendance reports updated
- Parent notified via email/app

---

## **9. Testing & Quality Assurance**

## **9. Deployment & Launch Plan**

### **Phase 1: Development** (Weeks 1-4)
- Backend API development
- Frontend UI development
- Database schema creation
- Basic feature implementation

### **Phase 2: Testing & Integration** (Weeks 5-6)
- Unit and integration testing
- Bug fixes and optimizations
- Security testing
- Performance optimization

### **Phase 3: UAT & Refinement** (Week 7)
- User acceptance testing
- Feedback incorporation
- Final bug fixes

### **Phase 4: Production Deployment** (Week 8)
- Database migration
- API deployment
- Mobile app submission to app stores
- Launch communications
- Monitoring and support

---

---

## **9. Testing & Quality Assurance**

### **9.1 Testing Strategy**
- **Unit Tests**: 80% code coverage for business logic (PHPUnit for backend, Jasmine for Angular)
- **Integration Tests**: API endpoint testing with real database
- **E2E Tests**: Critical user journeys (admin program creation, parent enrollment, attendance)
- **Performance Tests**: Load testing for 1,000+ concurrent users
- **Security Tests**: OWASP Top 10 protection verification, SQL injection tests
- **Accessibility Tests**: WCAG 2.1 AA compliance for admin interface

### **9.2 QA Process**
- Test environment mirrors production
- Automated test suite runs on every commit (CI/CD)
- Manual testing for UI/UX validation
- User acceptance testing (UAT) with stakeholders
- Production deployment validation checklist

---

## **10. Deployment & Launch Plan**

### **Phase 1: Development** (Weeks 1-4)
- Admin Dashboard: Program management, user management, reports
- API Backend: Authentication, program, enrollment, attendance endpoints
- Database: Schema creation and migrations
- Parent App: Basic UI screens

### **Phase 2: Testing & Integration** (Weeks 5-6)
- Unit and integration testing
- API endpoint validation
- Bug fixes and optimizations
- Security hardening
- Performance optimization

### **Phase 3: UAT & Refinement** (Week 7)
- User acceptance testing with staff
- Feedback incorporation
- Final bug fixes and polishing

### **Phase 4: Production Deployment** (Week 8)
- Database migration and backup strategy
- Admin and API deployment to hosting
- Mobile app submission to App Store & Google Play
- Launch communications
- 24/7 monitoring setup

---

## **11. Post-Launch Support & Maintenance**

### **11.1 Monitoring & Maintenance**
- 24/7 system monitoring with alerts
- Daily automated database backups (30-day retention)
- Weekly security updates and patches
- Monthly performance reviews and optimization
- Quarterly feature releases and enhancements

### **11.2 Support Channels**
- Email support: support@kcdfparents.org
- In-app help, FAQ, and troubleshooting guides
- Live chat during business hours
- Admin documentation and training materials

### **11.3 Future Enhancements**
- Advanced analytics and custom reports
- Parent-teacher messaging portal
- SMS notifications for critical updates
- Mobile wallet integration for payments
- Video tutorials for programs
- Parent community forums
- API for third-party integrations

---

## **12. Success Metrics & KPIs**

| Metric | Target | Measurement |
|--------|--------|-------------|
| Parent Registrations | 500+ in 6 months | Monthly tracking |
| Total Enrollments | 1000+ in 6 months | Per program tracking |
| System Uptime | 99.5% | Automated monitoring |
| Admin Dashboard Load Time | < 1 second | Performance testing |
| Mobile App Performance | < 3 second app launch | Device testing |
| User Satisfaction | 4.5+/5.0 | Survey and feedback |
| Support Response Time | < 24 hours | Support ticket tracking |
| Program Completion Rate | 85%+ | Attendance data |
| Mobile App Adoption | 70%+ of parents | App store analytics |

---

## **13. Architecture Summary**

### **Two-Application Architecture**

```
┌─────────────────────────────────────────────────────────────┐
│                     KCDF Parents System                      │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────────────┐      ┌──────────────────────┐     │
│  │   Admin Application   │      │    Parent App        │     │
│  │  (Slim + Bootstrap)   │      │ (Ionic + Angular)    │     │
│  │                       │      │                      │     │
│  │  • Program Mgmt       │      │  • Browse Programs   │     │
│  │  • Enrollments        │      │  • Enroll Child      │     │
│  │  • Attendance         │      │  • View Attendance   │     │
│  │  • Reports            │      │  • Manage Profile    │     │
│  │  • User Management    │      │  • Offline Mode      │     │
│  └──────────────────────┘      └──────────────────────┘     │
│            │                              │                  │
│            └──────────────────┬───────────┘                  │
│                               │                              │
│                ┌──────────────────────┐                     │
│                │  Shared Database     │                     │
│                │  (MySQL/MariaDB)     │                     │
│                │                      │                     │
│                │  • user_login        │                     │
│                │  • parents           │                     │
│                │  • children          │                     │
│                │  • programs          │                     │
│                │  • enrollments       │                     │
│                │  • activity_logs     │                     │
│                │  • etc.              │                     │
│                └──────────────────────┘                     │
│                                                               │
│                   ┌──────────────────────┐                  │
│                   │   API Backend        │                  │
│                   │   (Slim Framework)   │                  │
│                   │                      │                  │
│                   │  • JWT Authentication│                  │
│                   │  • REST Endpoints    │                  │
│                   │  • Data Validation   │                  │
│                   └──────────────────────┘                  │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

---

**Document Version**: 2.0  
**Last Updated**: May 24, 2026  
**Prepared By**: Development Team

---

## **Appendix: Technology Stack Summary**

| Component | Technology | Version |
|-----------|-----------|---------|
| **Admin Backend** | Slim Framework | 4.x |
| **Admin UI** | Bootstrap | 5.3+ |
| **Parent API** | Slim Framework | 4.x |
| **Mobile App** | Ionic + Angular | 7.x + 17.x |
| **Database** | MySQL/MariaDB | 5.7+ / 10.3+ |
| **Language (Backend)** | PHP | 8.0+ |
| **Language (Frontend)** | TypeScript | Latest |
| **Authentication** | JWT + Sessions | RFC 7519 |
| **Package Manager (PHP)** | Composer | 2.x |
| **Package Manager (Node)** | npm | 8+ |
| **Version Control** | Git | Latest |
| **Deployment** | Shared Hosting | cPanel/Plesk |
