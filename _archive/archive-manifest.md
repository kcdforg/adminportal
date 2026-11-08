# Archive manifest — Phase 08

**Status:** Approved for execution; all 59 exact source/destination pairs and the existing-HEAD snapshot scope were approved by the user.
**User direction:** Move all pre-existing files to the archive, do not delete them, and keep newly created files and folders in place.
**Inventory:** [`docs/development/documentation-disposition.md`](../docs/development/documentation-disposition.md)

## Scope definition

“Pre-existing files” is defined here as the 59 Git-tracked documentation candidates from the 74-file inventory, present unchanged at the current `HEAD` before this archival proposal. The 15 untracked documentation artifacts created during this baseline workflow are excluded and stay in place: `AGENTS.md`, `.github/copilot-instructions.md`, the ten `docs/architecture/` files, and `docs/development/{baseline-gap-report.md,feature-inventory.md,sdlc.md}`. The new disposition and manifest files, all `baseline-workflow/` deliverables, requirements, and other newly created files/folders also stay in place.

The changed `docs/.DS_Store` is not documentation and is excluded. `devdocs/coolify` is not one of the inventoried documentation candidates and is excluded. The three protected application directories are out of scope and must remain untouched.

## Exact proposed moves

Every destination preserves the source path under `_archive/pending-deletion/`. These are proposals only; no move is approved or executed yet. Content classifications and rationale are in the linked disposition report. Archiving is a move, not deletion; all archived files are to remain readable.

| # | Original path | Exact approved destination | Content classification | Approval |
|---:|---|---|---|---|
| 1 | `INVITATION-REGISTRATION-PROPOSAL.md` | `_archive/pending-deletion/INVITATION-REGISTRATION-PROPOSAL.md` | Needs human review | Approved |
| 2 | `PACKAGE_SUMMARY.md` | `_archive/pending-deletion/PACKAGE_SUMMARY.md` | Needs human review | Approved |
| 3 | `README.md` | `_archive/pending-deletion/README.md` | Needs human review | Approved |
| 4 | `docs/00-overview.md` | `_archive/pending-deletion/docs/00-overview.md` | Needs human review | Approved |
| 5 | `docs/01-database.md` | `_archive/pending-deletion/docs/01-database.md` | Needs human review | Approved |
| 6 | `docs/02-api-conventions.md` | `_archive/pending-deletion/docs/02-api-conventions.md` | Needs human review | Approved |
| 7 | `docs/adr/ADR-001-modular-monolith.md` | `_archive/pending-deletion/docs/adr/ADR-001-modular-monolith.md` | Needs human review | Approved |
| 8 | `docs/adr/ADR-002-shared-address-table.md` | `_archive/pending-deletion/docs/adr/ADR-002-shared-address-table.md` | Needs human review | Approved |
| 9 | `docs/adr/ADR-003-generic-entity-system.md` | `_archive/pending-deletion/docs/adr/ADR-003-generic-entity-system.md` | Needs human review | Approved |
| 10 | `docs/deployment/backend-installer.md` | `_archive/pending-deletion/docs/deployment/backend-installer.md` | Needs human review | Approved |
| 11 | `docs/deployment/hestiacp-deployment.md` | `_archive/pending-deletion/docs/deployment/hestiacp-deployment.md` | Needs human review | Approved |
| 12 | `docs/frontend/admin-portal.md` | `_archive/pending-deletion/docs/frontend/admin-portal.md` | Superseded/reference | Approved |
| 13 | `docs/frontend/parent-app.md` | `_archive/pending-deletion/docs/frontend/parent-app.md` | Superseded/reference | Approved |
| 14 | `docs/modules/academics.md` | `_archive/pending-deletion/docs/modules/academics.md` | Superseded target specification | Approved |
| 15 | `docs/modules/auth.md` | `_archive/pending-deletion/docs/modules/auth.md` | Superseded target specification | Approved |
| 16 | `docs/modules/community.md` | `_archive/pending-deletion/docs/modules/community.md` | Superseded target specification | Approved |
| 17 | `docs/modules/families.md` | `_archive/pending-deletion/docs/modules/families.md` | Superseded target specification | Approved |
| 18 | `docs/modules/notifications.md` | `_archive/pending-deletion/docs/modules/notifications.md` | Superseded target specification | Approved |
| 19 | `docs/modules/payments.md` | `_archive/pending-deletion/docs/modules/payments.md` | Superseded target specification | Approved |
| 20 | `docs/slim-backend-full-review-findings.md` | `_archive/pending-deletion/docs/slim-backend-full-review-findings.md` | Needs human review | Approved |
| 21 | `devdocs/ADMIN_APP_DEPLOY.md` | `_archive/pending-deletion/devdocs/ADMIN_APP_DEPLOY.md` | Reference / needs review | Approved |
| 22 | `devdocs/ADMIN_APP_TROUBLESHOOTING.md` | `_archive/pending-deletion/devdocs/ADMIN_APP_TROUBLESHOOTING.md` | Reference / needs review | Approved |
| 23 | `devdocs/ADMIN_APP_ZONELESS.md` | `_archive/pending-deletion/devdocs/ADMIN_APP_ZONELESS.md` | Historical | Approved |
| 24 | `devdocs/ARCHITECTURE_UPDATE.md` | `_archive/pending-deletion/devdocs/ARCHITECTURE_UPDATE.md` | Historical | Approved |
| 25 | `devdocs/DEPLOYMENT_GUIDE.md` | `_archive/pending-deletion/devdocs/DEPLOYMENT_GUIDE.md` | Reference / needs review | Approved |
| 26 | `devdocs/IMPLEMENTATION_STATUS.md` | `_archive/pending-deletion/devdocs/IMPLEMENTATION_STATUS.md` | Historical | Approved |
| 27 | `devdocs/PHASE_EXECUTION_GUIDE.md` | `_archive/pending-deletion/devdocs/PHASE_EXECUTION_GUIDE.md` | Obsolete as current guidance | Approved |
| 28 | `devdocs/PROJECT_REQUIREMENTS.md` | `_archive/pending-deletion/devdocs/PROJECT_REQUIREMENTS.md` | Superseded specification | Approved |
| 29 | `devdocs/UPDATED_REQUIREMENTS.md` | `_archive/pending-deletion/devdocs/UPDATED_REQUIREMENTS.md` | Duplicate legacy requirements | Approved |
| 30 | `devdocs/api-backend-php-comparison-analysis.md` | `_archive/pending-deletion/devdocs/api-backend-php-comparison-analysis.md` | Historical | Approved |
| 31 | `devdocs/updated-prompt.md` | `_archive/pending-deletion/devdocs/updated-prompt.md` | Duplicate legacy requirements | Approved |
| 32 | `prompts/adminapp/grouppage.md` | `_archive/pending-deletion/prompts/adminapp/grouppage.md` | Historical task prompt | Approved |
| 33 | `prompts/backend/group-features.md` | `_archive/pending-deletion/prompts/backend/group-features.md` | Historical task prompt | Approved |
| 34 | `prompts/kcdf-admin-remove-angular-material.md` | `_archive/pending-deletion/prompts/kcdf-admin-remove-angular-material.md` | Historical task prompt | Approved |
| 35 | `prompts/kcdf-admin-tailadmin-upgrade.md` | `_archive/pending-deletion/prompts/kcdf-admin-tailadmin-upgrade.md` | Historical task prompt | Approved |
| 36 | `prompts/migrate-slim-api-to-plain-php.md` | `_archive/pending-deletion/prompts/migrate-slim-api-to-plain-php.md` | Historical migration plan | Approved |
| 37 | `prompts/phase-1-database-schema.md` | `_archive/pending-deletion/prompts/phase-1-database-schema.md` | Historical execution prompt | Approved |
| 38 | `prompts/phase-2-slim-scaffold-auth.md` | `_archive/pending-deletion/prompts/phase-2-slim-scaffold-auth.md` | Historical execution prompt | Approved |
| 39 | `prompts/phase-3-families-members.md` | `_archive/pending-deletion/prompts/phase-3-families-members.md` | Historical execution prompt | Approved |
| 40 | `prompts/phase-4-academics.md` | `_archive/pending-deletion/prompts/phase-4-academics.md` | Historical execution prompt | Approved |
| 41 | `prompts/phase-5-payments.md` | `_archive/pending-deletion/prompts/phase-5-payments.md` | Historical execution prompt | Approved |
| 42 | `prompts/phase-6-community-notifications.md` | `_archive/pending-deletion/prompts/phase-6-community-notifications.md` | Historical execution prompt | Approved |
| 43 | `prompts/phase-7a-parent-app.md` | `_archive/pending-deletion/prompts/phase-7a-parent-app.md` | Historical execution prompt | Approved |
| 44 | `prompts/phase-7b-admin-portal.md` | `_archive/pending-deletion/prompts/phase-7b-admin-portal.md` | Historical execution prompt | Approved |
| 45 | `prompts/phase-8-swagger-api-documentation.md` | `_archive/pending-deletion/prompts/phase-8-swagger-api-documentation.md` | Historical execution prompt | Approved |
| 46 | `prompts/plain-php-migration/README.md` | `_archive/pending-deletion/prompts/plain-php-migration/README.md` | Historical execution material | Approved |
| 47 | `prompts/plain-php-migration/phase-0-inventory-and-contract.md` | `_archive/pending-deletion/prompts/plain-php-migration/phase-0-inventory-and-contract.md` | Historical execution prompt | Approved |
| 48 | `prompts/plain-php-migration/phase-1-runtime-and-endpoints.md` | `_archive/pending-deletion/prompts/plain-php-migration/phase-1-runtime-and-endpoints.md` | Historical execution prompt | Approved |
| 49 | `prompts/plain-php-migration/phase-2-authentication.md` | `_archive/pending-deletion/prompts/plain-php-migration/phase-2-authentication.md` | Historical execution prompt | Approved |
| 50 | `prompts/plain-php-migration/phase-3-families-and-identity.md` | `_archive/pending-deletion/prompts/plain-php-migration/phase-3-families-and-identity.md` | Historical execution prompt | Approved |
| 51 | `prompts/plain-php-migration/phase-4-academics-and-enrollment.md` | `_archive/pending-deletion/prompts/plain-php-migration/phase-4-academics-and-enrollment.md` | Historical execution prompt | Approved |
| 52 | `prompts/plain-php-migration/phase-5-payments.md` | `_archive/pending-deletion/prompts/plain-php-migration/phase-5-payments.md` | Historical execution prompt | Approved |
| 53 | `prompts/plain-php-migration/phase-6-community-and-notifications.md` | `_archive/pending-deletion/prompts/plain-php-migration/phase-6-community-and-notifications.md` | Historical execution prompt | Approved |
| 54 | `prompts/plain-php-migration/phase-7-parity-and-cutover.md` | `_archive/pending-deletion/prompts/plain-php-migration/phase-7-parity-and-cutover.md` | Historical execution prompt | Approved |
| 55 | `prompts/refactoring/REFACTORING_PLAN.md` | `_archive/pending-deletion/prompts/refactoring/REFACTORING_PLAN.md` | Historical task plan | Approved |
| 56 | `prompts/refactoring/REFACTORING_REVIEW.md` | `_archive/pending-deletion/prompts/refactoring/REFACTORING_REVIEW.md` | Historical task review | Approved |
| 57 | `prompts/refactoring/review.md` | `_archive/pending-deletion/prompts/refactoring/review.md` | Historical task review | Approved |
| 58 | `prompts/slim-backend-full-review.md` | `_archive/pending-deletion/prompts/slim-backend-full-review.md` | Historical task prompt | Approved |
| 59 | `rules/project-conventions.mdc` | `_archive/pending-deletion/rules/project-conventions.mdc` | Needs human review | Approved |

## Snapshot and execution boundaries

The approved pre-move snapshot is existing Git commit `3532e3e158195caa047cb4aebfb831c3a2b95187` (`HEAD`). The 59 listed tracked candidates are unchanged at that commit; current worktree status shows only `docs/.DS_Store` and newly created artifacts in these areas, all excluded from the move list. The user explicitly approved this existing `HEAD` as snapshot in lieu of a new commit; no new commit or staging is authorized or needed.

The in-scope inbound-link check found references from retained guidance and architecture documents. Necessary active references in retained files were updated to point to the archive destinations. Four archived documents were also adjusted to preserve moved references or correct a clear in-archive target; 55 archived files remain byte-identical to the approved snapshot. Relative links among files that move together preserve their directory relationships. Seven other local links in archived historical files point to targets absent at the approved snapshot; these are pre-existing unresolved references recorded in the Phase 08 report. Protected application directories remain out of scope and were not inspected or changed for this archival task.

Before execution, no move, rename, delete, stage, or commit had occurred. Move only the 59 approved files, preserve archived contents indefinitely, verify the one-to-one mapping and readability, and update the Phase 08 report/tracker. Never permanently delete.
