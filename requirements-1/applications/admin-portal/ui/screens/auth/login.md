# Admin portal login

This screen covers the admin sign-in experience for the web portal.

## Requirement summary

- The screen must display a username and password form for admin users.
- Required fields must be validated before submission.
- Users must be able to show or hide the password value.
- Valid submissions must call the authentication service and navigate to the dashboard on success.
- Login failures must show an error message and keep the user on the sign-in screen.

## Status

This is a working requirement draft intended for planning and review. It should be validated against runtime behavior before being treated as a final production requirement.

## UI-ADM-003-01 — Display the admin login form

The login screen displays the KCDF Admin Portal heading and a sign-in form.

**Acceptance criteria:**
- The page shows the admin portal title.
- The page includes a sign-in form for credentials.

## UI-ADM-003-02 — Capture username and password

The form provides separate username and password inputs.

**Acceptance criteria:**
- The form includes a username field.
- The form includes a password field.
- Both fields are required for submission.

## UI-ADM-003-03 — Show required-field validation

When the user submits an incomplete form, the application must clearly indicate which values are required.

**Acceptance criteria:**
- Empty username submission is flagged as invalid.
- Empty password submission is flagged as invalid.
- The user sees clear validation feedback before the request is sent.

## UI-ADM-003-04 — Toggle password visibility

The password visibility button switches the password input between concealed and visible text.

**Acceptance criteria:**
- The user can reveal the password value when needed.
- The user can hide it again after viewing.
- The action does not change the entered value itself.

## UI-ADM-003-05 — Submit login credentials

When the form is valid, selecting Sign In calls the authentication service with the form values.

**Acceptance criteria:**
- The form prevents submission while required fields are missing.
- A valid request sends the entered username and password to the auth service.
- The user is not navigated away before the request completes.

## UI-ADM-003-06 — Indicate login in progress

While the login request is pending, the Sign In button must indicate that processing is underway.

**Acceptance criteria:**
- The button is disabled while the authentication request is active.
- The interface shows a loading or signing-in indicator.
- The user cannot submit the same request repeatedly while it is in progress.

## UI-ADM-003-07 — Navigate after successful login

When the authentication service reports success, the application must move the user to the dashboard.

**Acceptance criteria:**
- A successful response transitions the user to the dashboard route.
- The sign-in screen is no longer displayed after successful authentication.
- The user continues in the authenticated admin experience.

## UI-ADM-003-08 — Show login errors

When the login request fails, the application must inform the user and keep the user on the sign-in screen.

**Acceptance criteria:**
- The user sees a visible error message when login fails.
- The message uses the server-provided value when available.
- The application remains on the login page, allowing retry.

**Previous screen-level ID:** `UI-ADM-003`. The original requirements `UI-ADM-003-01` through `UI-ADM-003-04` are superseded by the eight more atomic requirements above.