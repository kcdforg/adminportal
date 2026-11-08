# Phase 5 — Payments

## Context

Phases 0–4 are complete. Port the Slim Payments module as listed in the
inventory. Do not implement community or notifications in this phase.

## Preserve

- Role checks for listing, creating, viewing, and updating payments.
- Family-payment ownership checks and existing filters/pagination.
- Payment types, methods, status transitions, and required transaction
  references.
- Enrollment/family matching and active-family checks.
- Immutability of completed payments; refunds are separate payment records.
- Automatic `paid_at` behavior and recalculation of linked enrollment
  `payment_status` using completed payments minus completed refunds.
- Existing activity-log records, response shapes, and error codes.

Create payment writes, enrollment status recalculation, and activity logging
within one transaction. Use prepared queries and the shared PDO connection;
do not introduce a payment abstraction layer or change the schema.

## Exit checks

Add database-backed tests for authorization, payment validation, family and
enrollment consistency, immutable completed payments, pending-to-completed
transitions, refunds, all payment-status thresholds, and rollback if any
related operation fails. Confirm pagination and response compatibility.
