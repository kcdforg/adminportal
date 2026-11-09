# Loading Overlay

This document describes the shared indication that a page or action is loading.

## Status

Working draft.

### UI-ADM-SHR-LOD-001 — Communicate loading state

**Requirement:** The interface informs users when an operation is taking time to complete.

**Acceptance criteria:**
- The loading state is visible and, where appropriate, announced to assistive technology.
- The page does not present stale content as if it were the result of the pending request.
- Repeated submissions are prevented when they could create duplicate actions.
- The loading state ends when the operation succeeds or fails.
