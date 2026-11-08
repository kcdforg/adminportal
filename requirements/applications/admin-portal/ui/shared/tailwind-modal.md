# Modal Dialog

This document describes modal dialogs used by administration screens.

## Status

Working draft.

### UI-ADM-SHR-MOD-001 — Present modal content

**Requirement:** A modal presents a focused task without losing the user’s place in the underlying screen.

**Acceptance criteria:**
- The dialog has an accessible title and moves keyboard focus into the dialog.
- Users can complete or cancel the task using clear controls.
- Closing the dialog returns focus to the control that opened it when possible.
- Interacting inside the dialog does not accidentally trigger actions on the underlying page.
