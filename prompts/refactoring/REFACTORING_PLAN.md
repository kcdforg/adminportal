# Refactoring Plan

This plan is meant to simplify the migrated PHP API without changing its external behavior. It does not refactor the application directly; it defines safe, phased work for a later implementation pass.

## Phase 0 - Baseline and safety

### Objective
Capture the current request flow, route inventory, and response contracts before any structural simplification occurs.

### Files to Review
- `api/index.php`
- `api/routes.php`
- `config/init.php`
- `config/config.php`
- `api/auth/*.php`

### Changes
- Document the main route groups and expected payload structure.
- Confirm the JWT configuration and database bootstrap behavior.
- Record the baseline response formats for login, refresh, and general errors.
- Confirm which endpoints depend on the helper layer most heavily.

### Do Not Change
- Do not modify endpoint logic or database schema.
- Do not change authentication behavior or response format in this phase.

### Dependencies
- None.

### Risk
Low

### Verification
- Check that all routes still resolve correctly.
- Confirm that the project still boots without PHP errors.
- Capture a list of route handlers and response patterns before refactoring.

### Expected Result
A clean baseline of route handlers, JWT configuration, and response contract that can be used as a reference for all later phases.

### Copilot Prompt

```text
You are implementing Phase 0 of the project refactoring.

Before changing anything, inspect these files:
- api/index.php
- api/routes.php
- config/init.php
- config/config.php
- api/auth/*.php

Goal:
Document the current API baseline without changing behavior.

Make only the changes required for this phase.

Do not:
- introduce controllers
- introduce DTOs
- change database schema
- change API behavior
- change security behavior
- refactor later phases

This phase should only establish the route inventory, startup configuration, and response baseline.

After completing the work:
1. Review all modified files.
2. Confirm no PHP source behavior changed.
3. Summarize the route inventory and startup configuration.
4. Explain any routing or config findings that should be addressed later.
```

## Phase 1 - Common infrastructure cleanup

### Objective
Establish a single, consistent request/response helper layer and simplify the startup bootstrap without changing any business behavior.

### Files to Review
- `http/helpers.php`
- `services/auth.php`
- `config/init.php`
- `api/index.php`

### Changes
- Choose one canonical API helper set for JSON body parsing and response generation.
- Make the bootstrap flow simpler and consistent.
- Keep JWT validation in one place rather than duplicating checks.
- Standardize naming for request/response helpers.

### Do Not Change
- Do not modify business logic in endpoint scripts.
- Do not change database schema or JWT claims format.
- Do not change route registration.

### Dependencies
- Phase 0 complete.

### Risk
Low to Medium

### Verification
- Verify all API endpoints still return the same JSON envelope and status codes.
- Check startup behavior still loads config and database correctly.
- Confirm no route or response contract changes occurred.

### Expected Result
A single small set of reusable helpers for request parsing and response generation, with a consistent startup flow.

### Copilot Prompt

```text
You are implementing Phase 1 of the project refactoring.

Before changing anything, inspect:
- http/helpers.php
- services/auth.php
- config/init.php
- api/index.php

Goal:
Create a single, consistent request/response helper layer and tighten the bootstrap flow without changing API behavior.

Make only the changes required for this phase.

Do not:
- introduce controllers
- redesign the architecture
- change database schema
- refactor unrelated endpoints
- perform work from later phases
- alter response formats beyond the existing contract

Keep JWT validation centralized and simple.

After completing the changes:
1. Review all modified files.
2. Check for syntax errors.
3. Confirm the API still returns the same JSON payload structure and status codes.
4. Summarize the files changed and the helper layer that remains.
```

## Phase 2 - Authentication cleanup

### Objective
Separate JWT authentication logic from business authorization logic while preserving token generation, validation, and claims handling.

### Files to Review
- `services/auth.php`
- `services/identity.php`
- `api/auth/login.php`
- `api/auth/refresh.php`
- `api/auth/me.php`
- `api/auth/logout.php`

### Changes
- Keep authentication functions focused on JWT parsing, validation, and claims.
- Remove or reduce generic auth wrappers that also make authorization decisions.
- Preserve the existing token format, refresh-token hashing, and claim semantics.

### Do Not Change
- Do not change the token signing algorithm or claim structure unless the project already documents it.
- Do not change refresh token storage requirements.
- Do not modify login/refresh business rules beyond the auth-layer cleanup.

### Dependencies
- Phase 1 complete.

### Risk
Medium

### Verification
- Exercise login, refresh, and `GET /api/v1/auth/me` flows.
- Confirm JWT validation still triggers on invalid or expired tokens.
- Confirm the response payloads remain compatible.

### Expected Result
Authentication is explicit and easy to read; authorization rules are separated into dedicated permission checks.

### Copilot Prompt

```text
You are implementing Phase 2 of the project refactoring.

Before changing anything, inspect:
- services/auth.php
- services/identity.php
- api/auth/login.php
- api/auth/refresh.php
- api/auth/me.php
- api/auth/logout.php

Goal:
Keep JWT authentication focused on token validation only. Separate auth from authorization logic without changing token behavior.

Make only the changes required for this phase.

Do not:
- introduce framework-like middleware
- redesign the auth system
- change database schema
- change response payload format unexpectedly
- refactor later phases

Authentication and authorization must remain separate.
Use a clear authentication helper such as checkAuth() or authenticate() only for JWT verification.

After completing the changes:
1. Review all modified files.
2. Check for syntax errors.
3. Verify login, refresh, and profile retrieval still work.
4. Summarize the changed auth flow and any remaining authorization logic that still needs cleanup.
```

## Phase 3 - Authorization separation

### Objective
Move family/member/project/session/group access logic into clear, explicit permission checks rather than a shared generic access layer.

### Files to Review
- `services/identity.php`
- `services/academics.php`
- `services/community-notifications.php`
- representative endpoints such as `api/families/*.php`, `api/groups/*.php`, `api/sessions/*.php`, `api/programs/*.php`

### Changes
- Split generic access checks into explicit functions with clear names.
- Keep the boolean permission checks readable and domain-specific.
- Ensure each route still rejects unauthorized users in the same way.

### Do Not Change
- Do not change the database schema.
- Do not change JWT claims or role values unless a later phase explicitly requires this.
- Do not refactor all endpoints at once; keep the scope focused on access checks.

### Dependencies
- Phase 2 complete.

### Risk
Medium

### Verification
- Confirm a valid member can access allowed data and denied members still receive 403-style responses.
- Validate admin and trainer access conditions.
- Check that family, session, and group access rules still behave the same.

### Expected Result
Access-control logic is readable, domain-specific, and separated from authentication.

### Copilot Prompt

```text
You are implementing Phase 3 of the project refactoring.

Before changing anything, inspect:
- services/identity.php
- services/academics.php
- services/community-notifications.php
- api/families/*.php
- api/groups/*.php
- api/sessions/*.php
- api/programs/*.php

Goal:
Separate authorization logic from authentication and keep permission checks explicit and readable.

Make only the changes required for this phase.

Do not:
- introduce policy classes
- introduce middleware classes
- redesign the architecture
- change database schema
- change JWT claims
- refactor unrelated endpoints
- perform work from later phases

Authorization should be implemented as explicit permission checks such as canViewFamily(), canEditMember(), canManageBatch(), etc.

After completing the changes:
1. Review all modified files.
2. Check for syntax errors.
3. Verify access-control behavior for valid and invalid users is preserved.
4. Summarize the permission checks you extracted and any remaining complex rules.
```

## Phase 4 - Validation consolidation

### Objective
Reduce repeated validation rules and keep them near the relevant domain instead of in a shared global helper.

### Files to Review
- `services/identity.php`
- `services/academics.php`
- `services/community-notifications.php`
- representative endpoint files where validation rules are repeated

### Changes
- Consolidate repeated validation routines into a smaller set of domain-specific validators.
- Preserve existing validation messages and status codes.
- Remove obvious duplication where the same check is repeated across helpers.

### Do Not Change
- Do not change validation behavior or error codes unexpectedly.
- Do not change database schema.
- Do not remove required security validations.

### Dependencies
- Phase 3 complete.

### Risk
Medium

### Verification
- Validate representative create/update flows for members, families, programs, groups, and notifications.
- Confirm 422 responses still appear for invalid input.
- Confirm required fields and allowed values remain the same.

### Expected Result
Validation rules are easier to review, and each entity has a focused validation function rather than a giant all-purpose validator.

### Copilot Prompt

```text
You are implementing Phase 4 of the project refactoring.

Before changing anything, inspect:
- services/identity.php
- services/academics.php
- services/community-notifications.php
- representative endpoint files with repeated validation logic

Goal:
Reduce duplicated validation while preserving the same API error contracts and behavior.

Make only the changes required for this phase.

Do not:
- change validation semantics unexpectedly
- change database schema
- alter security checks
- refactor unrelated files
- perform work from later phases

Keep validation simple, domain-specific, and explicit. Preserve existing status codes and validation messages.

After completing the changes:
1. Review all modified files.
2. Check for syntax errors.
3. Verify invalid input still returns the same 422-style validation failures.
4. Summarize the validation rules consolidated and list any remaining duplicates.
```

## Phase 5 - Endpoint simplification

### Objective
Remove callback-heavy endpoint wrappers and make each route read like a simple PHP flow: auth → validate → authorize → operation → respond.

### Files to Review
- `api/families/*.php`
- `api/groups/*.php`
- `api/trainers/*.php`
- `api/programs/*.php`
- `api/sessions/*.php`
- `api/enrollments/*.php`

### Changes
- Replace nested `kcdf_identity_run()` callback patterns with direct procedural flow.
- Keep early returns and explicit error handling.
- Preserve business logic and response payloads.

### Do Not Change
- Do not rewrite unrelated endpoints.
- Do not change route definitions or database schema.
- Do not introduce abstractions that do not directly improve clarity.

### Dependencies
- Phases 1-4 complete.

### Risk
High

### Verification
- Smoke test representative endpoints from each major module.
- Confirm success and error responses remain unchanged.
- Review the route files for flow readability and early returns.

### Expected Result
Endpoints are straightforward, consistent with the project philosophy, and much easier to read from top to bottom.

### Copilot Prompt

```text
You are implementing Phase 5 of the project refactoring.

Before changing anything, inspect:
- api/families/*.php
- api/groups/*.php
- api/trainers/*.php
- api/programs/*.php
- api/sessions/*.php
- api/enrollments/*.php

Goal:
Simplify endpoint flow so each route reads as a clear top-to-bottom PHP process: auth → validate → authorize → operate → respond.

Make only the changes required for this phase.

Do not:
- change route definitions
- change database schema
- introduce controllers, DTOs, middleware classes, or service providers
- refactor unrelated files
- perform work from later phases

Prefer explicit early returns and simple procedural flow over nested callbacks.

After completing the changes:
1. Review all modified files.
2. Check for syntax errors.
3. Validate representative success and error responses for the updated endpoints.
4. Summarize the endpoint flow improvements and note any remaining complex logic.
```

## Phase 6 - Service and repository cleanup

### Objective
Keep only the abstractions that provide genuine value and remove generic wrappers that exist only to polish the codebase.

### Files to Review
- `repositories/ProfileRepository.php`
- `services/identity.php`
- `services/academics.php`
- `services/community-notifications.php`
- `services/auth.php`

### Changes
- Keep complex reused queries in a repository only if they are genuinely shared.
- Trim trivial wrappers that only call a Medoo helper and do not add business value.
- Preserve security and access rules while reducing abstraction.

### Do Not Change
- Do not change the database schema.
- Do not change external API contracts.
- Do not remove security checks that are necessary.

### Dependencies
- Phase 5 complete.

### Risk
Medium

### Verification
- Check that the routes still behave the same.
- Confirm all query behavior remains intact.
- Review a subset of modules in which repository/service boundaries were simplified.

### Expected Result
Simple CRUD-like code stays simple, and only genuinely reused or complex database logic remains abstracted.

### Copilot Prompt

```text
You are implementing Phase 6 of the project refactoring.

Before changing anything, inspect:
- repositories/ProfileRepository.php
- services/identity.php
- services/academics.php
- services/community-notifications.php
- services/auth.php

Goal:
Remove unnecessary service/repository abstraction while keeping the code readable and the behavior unchanged.

Make only the changes required for this phase.

Do not:
- change database schema
- change API contracts
- remove required security logic
- redesign the architecture
- refactor unrelated files
- perform work from later phases

Keep repositories only for genuinely reused or complex SQL. Prefer direct Medoo queries for simple operations.

After completing the changes:
1. Review all modified files.
2. Check for syntax errors.
3. Verify API behavior remains unchanged.
4. Summarize which abstractions remain and which were removed.
```

## Phase 7 - Final cleanup and consistency review

### Objective
Perform the final pass for naming, dead code, repeated helpers, and consistency without altering the real business behavior.

### Files to Review
- `services/*.php`
- `http/helpers.php`
- `api/**/*.php`
- `repositories/*.php`

### Changes
- Remove or rename stale helper functions that are no longer needed.
- Standardize naming and keep functions purpose-driven.
- Ensure no dead or redundant helper remains in the final structure.
- Re-run a final consistency review focused on security, auth, and access logic.

### Do Not Change
- Do not change business rules or database schema.
- Do not change JWT or password hashing behavior.
- Do not introduce a framework or new architectural pattern.

### Dependencies
- All prior phases complete.

### Risk
Low to Medium

### Verification
- Review all modified files for final consistency.
- Perform a final search for stale, duplicate, or generic helper names.
- Check the API still boots and representative routes continue to work.

### Expected Result
A simplified, consistent project that follows the intended lightweight PHP architecture and is easier to maintain.

### Copilot Prompt

```text
You are implementing Phase 7 of the project refactoring.

Before changing anything, inspect:
- services/*.php
- http/helpers.php
- api/**/*.php
- repositories/*.php

Goal:
Perform a final cleanup pass focused on naming, dead code, and consistency while preserving all API behavior and security requirements.

Make only the changes required for this phase.

Do not:
- change database schema
- change JWT or password security behavior
- introduce a framework or new architecture
- refactor unrelated logic
- alter API contracts

Keep the project lightweight, readable, and consistent with the intended PHP architecture.

After completing the changes:
1. Review all modified files.
2. Check for syntax errors.
3. Confirm a subset of representative routes still works after cleanup.
4. Summarize the final consistency improvements and note any issues that remain intentionally untouched.
```

## Recommended Implementation Order

The safest sequence is:

1. Phase 0: Baseline and safety
2. Phase 1: Common infrastructure cleanup
3. Phase 2: Authentication cleanup
4. Phase 3: Authorization separation
5. Phase 4: Validation consolidation
6. Phase 5: Endpoint simplification
7. Phase 6: Service and repository cleanup
8. Phase 7: Final cleanup and consistency review

This ordering keeps the refactor incremental and reduces the risk of breaking the API while preserving the project’s lightweight PHP style.
