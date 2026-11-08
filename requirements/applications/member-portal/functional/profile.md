# Member Profile

This document describes the member’s profile and account information in the member portal.

## Status

Working draft. Confirm profile edit permissions and the data that is visible to the member.

### MEM-PRO-001 — View own profile

**Actor:** Signed-in members.

**Requirement:** A member can view their own profile and relevant account information.

**Acceptance criteria:**
- The portal displays only information associated with the signed-in member.
- Missing optional profile details do not prevent the rest of the profile from loading.
- The member can distinguish profile information from account credentials.

### MEM-PRO-002 — Update profile information

**Actor:** Signed-in members, where self-service updates are permitted.

**Requirement:** A member can update profile fields that the organization permits members to maintain.

**Acceptance criteria:**
- The portal makes editable and read-only information clear.
- Required fields and accepted formats are validated.
- A successful change is confirmed; a failed change does not appear successful.

## Open questions

- Define which profile fields members can edit and which require staff assistance.
- Confirm whether account credentials can be changed from the profile area.
- Define how profile changes to family or contact information are reviewed.
