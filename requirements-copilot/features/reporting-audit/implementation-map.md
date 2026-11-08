# Dashboard, reports and audit — implementation map

| ID | Backend implementation | Client / schema | Existing test source | Disposition |
|---|---|---|---|---|
| DASH-001 | Family, batch, payment list handlers and routes; payment list role helper | `kcdf-admin-app/src/app/features/dashboard/dashboard.component.ts`; family/batch/payment services; notification placeholder | No dashboard/report test identified | **Partial**; role-dependent payment list can prevent summary; reports missing |
| AUD-001 | `api/activity-logs/index.php`; `services/identity.php::logActivity`; GET route only | Admin `core/services/activity-log.service.ts`, audit-log list; `activity_logs` | No activity-log test identified in bounded inventory | **Partial**; UI field/date filter mismatch |

## Missing/unmapped surfaces

| Surface | Evidence | Disposition |
|---|---|---|
| Attendance, enrollment, payment report pages | Admin `features/reports/{attendance-report,enrollment-report,payment-report}/`; no matching route in `api-backend-php/api/routes.php` or operation in `swagger/openapi.json` | **Missing backend endpoint / no mapped requirement behavior** |
| Notification dashboard count | Component placeholder; no fetched count identified | **Uncertain / placeholder**, no count contract inferred |
| Activity-log update/delete | No corresponding route registered | **Not present** in checked route registry |

No report APIs or calculations are proposed as existing functionality. Report pages remain UI/API gaps only.
