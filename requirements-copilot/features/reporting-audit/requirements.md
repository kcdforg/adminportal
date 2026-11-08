# Dashboard, reports and audit requirements

**Status:** Provisional source reconstruction. Dashboard and activity-log paths are observed; report API behavior is not established.

## Requirements

### DASH-001 — Display dashboard summaries

- **Actor:** Authenticated admin.
- **Requirement:** The dashboard shall request family, active-batch and pending-payment summary totals using paginated list metadata.
- **Acceptance criteria (source-derived):**
  - Summary requests ask for a minimal page and read total values from response metadata.
  - The notification card displays a placeholder rather than a fetched count in the inspected component.
- **Evidence:** Admin `features/dashboard/dashboard.component.ts`; family, batch and payment services.
- **Status:** Client source observed; runtime/API completion is unverified.

### AUD-001 — Record and query activity logs

- **Actor:** System write handlers; super-admin reader.
- **Requirement:** Source-observed write handlers shall append activity records, and the API shall allow a super admin to list/filter those records.
- **Acceptance criteria (source-derived):**
  - Activity rows include actor profile ID when available, action/entity identifiers, optional old/new values and timestamp.
  - Listing is super-admin-only, newest-first, paginated and supports the server-defined filters.
  - No update/delete route is registered for activity logs.
- **Evidence:** `services/identity.php` (`logActivity`); `api/activity-logs/index.php`; `schema.sql` (`activity_logs`); `api/routes.php`.
- **Status:** Backend source observed; admin audit UI uses mismatched date/actor field names.

## Partial report surfaces (no report requirement inferred)

The Angular admin contains attendance, enrollment and payment report pages that call `/reports/attendance`, `/reports/enrollments` and `/reports/payments`. No matching PHP route/handler or checked OpenAPI operation was found. The pages' existence and labels do not establish report calculations, data access rules, exports or expected results. These remain documented UI/API gaps, not functional requirements.

## Unresolved

Dashboard failure messaging, the meaning of the notification placeholder, report calculations and report authorization are not established. Do not assign new report behavior without approved requirements evidence.
