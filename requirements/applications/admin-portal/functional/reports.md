# Reports

This document captures the admin portal report requirements for attendance, payments, and enrollments.

## Requirement summary

- Authorized administrators should be able to open each reporting screen.
- The system should allow the user to set the relevant filters before generating or viewing a report.
- Report data should be displayed in a structured format that supports review and decision-making.
- Users should be able to export a report when the business flow supports it.
- Invalid filter values, missing data, or failed report generation should be handled with clear feedback.

## Status

This is a working requirement draft for planning and review. It should be validated against live runtime behavior before being treated as a final production requirement.

### ADM-RPT-001 — View attendance report

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `RPT-001`
**Actor:** Administrators.

**Requirement:** An administrator can open the attendance report page and request attendance data for a selected batch and optional date range.

**Acceptance criteria:**
- The attendance report screen is available to authorized administrators.
- The user can select a batch and optionally a date range or similar filters.
- The report displays attendance data in a readable table or summary format.
- The user can export the attendance report if the report flow supports CSV output.
- If the report cannot be generated, the system shows a clear error message instead of failing silently.

---

### ADM-RPT-002 — View payment report

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `RPT-002`
**Actor:** Administrators.

**Requirement:** An administrator can open the payment report page and request payment-reporting data for the relevant scope.

**Acceptance criteria:**
- The payment report screen is available to authorized administrators.
- The user can apply the relevant date or grouping filters for payment review.
- The report displays payment totals or transaction details in a readable format.
- The user can review the result without needing to navigate away from the page.
- Errors in loading or generating the report are surfaced clearly to the user.

---

### ADM-RPT-003 — View enrollment report

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `RPT-003`
**Actor:** Administrators.

**Requirement:** An administrator can open the enrollment report page and request enrollment data for the selected reporting scope.

**Acceptance criteria:**
- The enrollment report screen is available to authorized administrators.
- The user can apply filters relevant to the enrollment view, such as batch or date ranges where applicable.
- The report output is displayed clearly for review and analysis.
- Export actions, if enabled, use the current visible report data.
- If the reporting request fails, the system shows a clear error and keeps the user on the page.

---

### ADM-RPT-004 — Handle report errors gracefully

**Type:** Functional
**Owner:** admin-portal
**Legacy ID:** `RPT-004`
**Actor:** Administrators.

**Requirement:** Report generation failures and invalid filter values must be handled in a user-friendly way.

**Acceptance criteria:**
- Missing or invalid selection parameters are flagged before or during the request.
- report-generation failures show a clear message to the user.
- The page remains usable after a failed request so the user can retry or adjust filters.
- The system does not expose raw backend errors unless they are appropriate for the current user role and context.