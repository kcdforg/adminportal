# Trainers

This document covers the administration of trainer profiles and trainer records.

## Status

Working draft. Confirm trainer data policy and whether batch or session assignments belong in this workflow.

### ADM-TRN-001 — Browse and view trainers

**Actor:** Administrators with trainer-view access.

**Requirement:** An authorized administrator can browse trainer records and open an individual trainer profile.

**Acceptance criteria:**
- The list shows each trainer’s code, name, specialization when available, and current status.
- The user can search trainers and filter the list by status.
- The list supports pagination and shows a clear empty state when no trainers match.
- Opening a trainer shows the profile, contact details, address when available, and trainer information.
- A missing or inaccessible trainer is not presented as a valid empty profile.

### ADM-TRN-002 — Create a trainer

**Actor:** Administrators with trainer-management access.

**Requirement:** An authorized administrator can create a trainer record with its associated profile information.

**Acceptance criteria:**
- First and last name are required.
- Optional profile information includes date of birth, gender, mobile number, and email address.
- Optional trainer information includes specialization, years of experience, join date, and biography.
- Years of experience, when provided, must be a whole number from 0 through 60.
- An address is optional; when any address details are entered, address line 1 and city are required.
- The system prevents a profile from being registered as a trainer more than once.
- A successful save confirms that the trainer was created and makes the record available in the list.
- If any part of saving fails, the user sees a clear error and the system does not leave an unexplained partial trainer record.

### ADM-TRN-003 — Edit trainer information and status

**Actor:** Administrators with trainer-management access.

**Requirement:** An authorized administrator can update an existing trainer’s permitted profile information, trainer information, address, and status.

**Acceptance criteria:**
- The edit form is pre-populated with the current values.
- The user can update profile and trainer details without creating a duplicate trainer.
- Status is one of Active, Inactive, or On Leave.
- Invalid values are rejected with clear feedback, and entered changes remain available for correction.
- A successful update is confirmed and the refreshed values appear in the detail and list views.
- A failed update is not shown as successful.

## Open questions

- Confirm which personal and contact fields are required and which roles may view them.
- Confirm whether trainers can be assigned to batches or sessions and, if so, define those rules in a separate requirement.
- Confirm how trainer status changes affect future and existing sessions.
- Resolve and verify the trainer update request contract before treating portal editing as an end-to-end capability.
