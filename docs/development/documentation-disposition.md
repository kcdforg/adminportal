# Historical documentation disposition

**Review status:** Inventory and classification complete; exact archival moves approved and completed.
**Scope:** 74 documentation/guidance candidates outside `api-backend-php/`, `kcdf-admin-app/`, and `kcdf-parents-app/`.
**Result:** The initial evidence-based review recommended no moves. Following the user's direction, 59 pre-existing Git-tracked documentation candidates were moved to the exact destinations approved in [`_archive/archive-manifest.md`](../../_archive/archive-manifest.md). No content was deleted.

## Method and limitations

Candidates were classified by their apparent role, uniqueness, relationship to the current source-derived architecture and requirements, and known active links. “Superseded” means the document must not be treated as the current baseline; it does not mean its unique detail should be discarded. Historical prompts are records of prior work, not authorization to execute them. The review did not validate runtime/deployment applicability or resolve product-policy questions. No application directories were reviewed for this archive inventory.

The inventory found 30 files under `docs/`, 11 under `devdocs/`, 27 under `prompts/`, four root Markdown files, one `.github/` file, and one `.mdc` file. No `.cursor/` directory or documentation files under `scripts/` were found; `scripts/env-xampp.sh` is executable rather than documentation and was excluded. `requirements/` and active `baseline-workflow/` deliverables were outside the candidate set.

The 59 archived sources were the tracked, pre-existing candidates. The 15 untracked documentation artifacts created during this baseline workflow remain in place: `AGENTS.md`, `.github/copilot-instructions.md`, the ten `docs/architecture/` files, and `docs/development/baseline-gap-report.md`, `docs/development/feature-inventory.md`, and `docs/development/sdlc.md`. The disposition labels below describe content status; for archived sources, “preserve in archive” means keep the content at its archive destination, not delete it. The user approved the exact manifest and snapshot scope before the moves.

## Dispositions

| Candidate | Content classification | Replacement / unique information and reason to preserve |
|---|---|---|
| `AGENTS.md` | Authoritative; retain | Current repository guidance; links to SDLC and requirements and marks legacy conventions. |
| `.github/copilot-instructions.md` | Authoritative; retain | Current Copilot guidance; links to AGENTS, SDLC and requirements and flags legacy conventions. |
| `docs/architecture/admin-architecture.md` | Authoritative; retain | Current source-evidence architecture; no archival replacement needed. |
| `docs/architecture/api-contract-audit.md` | Authoritative; retain | Current static route/OpenAPI comparison evidence. |
| `docs/architecture/backend-architecture.md` | Authoritative; retain | Current source-evidence architecture; runtime claims remain qualified. |
| `docs/architecture/database-architecture.md` | Authoritative; retain | Current source-evidence database architecture. |
| `docs/architecture/deployment-architecture.md` | Authoritative; retain | Current evidence and deployment uncertainties. |
| `docs/architecture/existing-system-inventory.md` | Authoritative; retain | Current inventory evidence. |
| `docs/architecture/integration-architecture.md` | Authoritative; retain | Current source-evidence integration architecture. |
| `docs/architecture/member-architecture.md` | Authoritative; retain | Current source-evidence member application architecture. |
| `docs/architecture/security-architecture.md` | Authoritative; retain | Current source-evidence security architecture. |
| `docs/architecture/system-architecture.md` | Authoritative; retain | Current source-evidence system view. |
| `docs/development/baseline-gap-report.md` | Authoritative; retain | Current static gaps and unresolved policy questions. |
| `docs/development/sdlc.md` | Authoritative; retain | Current development workflow. |
| `rules/project-conventions.mdc` | Needs human review; preserve in archive | Contains unique always-on rules, but repository layout/design assertions are flagged as legacy by AGENTS and Copilot. Active references in retained guidance now point to the proposed archive location. |
| `README.md` | Needs human review; retain | Describes an older project layout/deployment and links to paths that do not match the current documentation set. Architecture and deployment docs are only partial replacements; link correction needs review. |
| `PACKAGE_SUMMARY.md` | Needs human review; retain | Older package/deployment framing with potentially useful setup/checklist detail. `docs/deployment/` is only a partial replacement. |
| `INVITATION-REGISTRATION-PROPOSAL.md` | Needs human review; retain | Proposed workflow and decision points are not accepted policy. The invitations/entities requirements provide feature context, not a replacement for the proposal or decisions. |
| `docs/00-overview.md` | Needs human review; retain | Older target architecture makes distinct repository, stack and deployment assertions. New architecture docs are partial context, not proof the old proposal is resolved. |
| `docs/01-database.md` | Needs human review; retain | Detailed legacy schema/design claims and unique schema detail; linked by older module documents. Current database architecture and feature requirements are partial replacements. |
| `docs/02-api-conventions.md` | Needs human review; retain | Unique target API conventions and links from older module/phase materials. API audit and feature requirements are partial replacements. |
| `docs/adr/ADR-001-modular-monolith.md` | Needs human review; retain | Unique recorded decision; “Accepted” status requires confirmation. |
| `docs/adr/ADR-002-shared-address-table.md` | Needs human review; retain | Unique recorded decision; “Accepted” status requires confirmation. |
| `docs/adr/ADR-003-generic-entity-system.md` | Needs human review; retain | Unique recorded decision; “Accepted” status requires confirmation. |
| `docs/deployment/backend-installer.md` | Needs human review; retain | Distinct operational procedure; applicability must be verified. Deployment architecture is only partial context. |
| `docs/deployment/hestiacp-deployment.md` | Needs human review; retain | Distinct operational procedure; applicability must be verified. Deployment architecture is only partial context. |
| `docs/slim-backend-full-review-findings.md` | Needs human review; retain | Unique review findings; review before treating as current or replacing them. |
| `docs/development/feature-inventory.md` | Historical; retain | Phase 01 discovery snapshot, superseded as the current feature baseline by `requirements/index.md`, feature sets, and the baseline gap report. |
| `docs/modules/auth.md` | Superseded target specification; retain | Unique rules/API detail; current feature replacement is `requirements/features/authentication-access/`. |
| `docs/modules/families.md` | Superseded target specification; retain | Unique rules/API detail; current feature context is `requirements/features/members-families/` and `requirements/features/invitations-entities/`. |
| `docs/modules/academics.md` | Superseded target specification; retain | Unique rules/API detail; current feature context is `requirements/features/programs-participation/`. |
| `docs/modules/payments.md` | Superseded target specification; retain | Unique rules/API detail; current feature context is `requirements/features/payments-communications/`. |
| `docs/modules/community.md` | Superseded target specification; retain | Unique rules/API detail; current feature context is `requirements/features/groups-community/` and `requirements/features/invitations-entities/`. |
| `docs/modules/notifications.md` | Superseded target specification; retain | Unique rules/API detail; current feature context is `requirements/features/payments-communications/` and `requirements/features/reporting-audit/`. |
| `docs/frontend/admin-portal.md` | Superseded/reference; retain | Unique screen/structure detail; admin architecture and feature requirements are partial replacements. |
| `docs/frontend/parent-app.md` | Superseded/reference; retain | Unique screen/structure detail; member architecture and feature requirements are partial replacements. |
| `devdocs/ARCHITECTURE_UPDATE.md` | Historical; retain | Unique architecture/requirements evolution context; newer architecture and requirements supersede it as current guidance. |
| `devdocs/PROJECT_REQUIREMENTS.md` | Superseded specification; retain | Unique narrative/use-case detail; requirements index and feature sets replace it as the current baseline. Review before discarding. |
| `devdocs/UPDATED_REQUIREMENTS.md` | Duplicate legacy requirements material; retain | Duplicated by `devdocs/updated-prompt.md`; current baseline is the requirements index/sets. Preserve until references are reviewed. |
| `devdocs/updated-prompt.md` | Duplicate legacy requirements material; retain | Duplicated by `devdocs/UPDATED_REQUIREMENTS.md`; legacy status is referenced by rules and the old execution guide. Preserve until references are reviewed. |
| `devdocs/IMPLEMENTATION_STATUS.md` | Historical; retain | Unique period-specific implementation snapshot; no exact snapshot replacement. |
| `devdocs/PHASE_EXECUTION_GUIDE.md` | Obsolete as current execution guidance; retain | Process guidance is replaced by SDLC/AGENTS, but phase-specific links and many legacy-document references need link review. |
| `devdocs/DEPLOYMENT_GUIDE.md` | Reference / needs review; retain | Distinct operational instructions; verify applicability. Deployment architecture is only partial context. |
| `devdocs/ADMIN_APP_DEPLOY.md` | Reference / needs review; retain | Unique deployment checklist; verify applicability. Deployment architecture is only partial context. |
| `devdocs/ADMIN_APP_TROUBLESHOOTING.md` | Reference / needs review; retain | Unique troubleshooting notes; verify before use; no exact replacement. |
| `devdocs/ADMIN_APP_ZONELESS.md` | Historical; retain | Unique implementation-status snapshot, not an authoritative current-state claim. |
| `devdocs/api-backend-php-comparison-analysis.md` | Historical; retain | Unique comparison analysis; no exact replacement. |
| `prompts/phase-1-database-schema.md` | Historical execution prompt; retain | Unique prior implementation instructions; not current authorization or requirements. |
| `prompts/phase-2-slim-scaffold-auth.md` | Historical execution prompt; retain | Unique prior implementation instructions; not current authorization or requirements. |
| `prompts/phase-3-families-members.md` | Historical execution prompt; retain | Unique prior implementation instructions; not current authorization or requirements. |
| `prompts/phase-4-academics.md` | Historical execution prompt; retain | Unique prior implementation instructions; not current authorization or requirements. |
| `prompts/phase-5-payments.md` | Historical execution prompt; retain | Unique prior implementation instructions; not current authorization or requirements. |
| `prompts/phase-6-community-notifications.md` | Historical execution prompt; retain | Unique prior implementation instructions; not current authorization or requirements. |
| `prompts/phase-7a-parent-app.md` | Historical execution prompt; retain | Superseded as current feature baseline by related requirements; retains prompt-specific detail. |
| `prompts/phase-7b-admin-portal.md` | Historical execution prompt; retain | Superseded as current feature baseline by related requirements; retains prompt-specific detail. |
| `prompts/phase-8-swagger-api-documentation.md` | Historical execution prompt; retain | API contract audit provides related current context, not a replacement for the prompt's distinct instructions. |
| `prompts/plain-php-migration/README.md` | Historical execution material; retain | Migration context and instructions; not current authorization. |
| `prompts/plain-php-migration/phase-0-inventory-and-contract.md` | Historical execution prompt; retain | Unique phase instructions; not current authorization. |
| `prompts/plain-php-migration/phase-1-runtime-and-endpoints.md` | Historical execution prompt; retain | Unique phase instructions; not current authorization. |
| `prompts/plain-php-migration/phase-2-authentication.md` | Historical execution prompt; retain | Unique phase instructions; not current authorization. |
| `prompts/plain-php-migration/phase-3-families-and-identity.md` | Historical execution prompt; retain | Unique phase instructions; not current authorization. |
| `prompts/plain-php-migration/phase-4-academics-and-enrollment.md` | Historical execution prompt; retain | Unique phase instructions; not current authorization. |
| `prompts/plain-php-migration/phase-5-payments.md` | Historical execution prompt; retain | Unique phase instructions; not current authorization. |
| `prompts/plain-php-migration/phase-6-community-and-notifications.md` | Historical execution prompt; retain | Unique phase instructions; not current authorization. |
| `prompts/plain-php-migration/phase-7-parity-and-cutover.md` | Historical execution prompt; retain | Unique phase instructions; not current authorization. |
| `prompts/migrate-slim-api-to-plain-php.md` | Historical migration plan; retain | Architecture/baseline docs provide context but do not replace the plan. |
| `prompts/adminapp/grouppage.md` | Historical task prompt; retain | Distinct task instructions; no exact durable replacement established. |
| `prompts/backend/group-features.md` | Historical task prompt; retain | Distinct task instructions; no exact durable replacement established. |
| `prompts/kcdf-admin-remove-angular-material.md` | Historical task prompt; retain | Distinct task instructions; no exact durable replacement established. |
| `prompts/kcdf-admin-tailadmin-upgrade.md` | Historical task prompt; retain | Distinct task instructions; no exact durable replacement established. |
| `prompts/refactoring/REFACTORING_PLAN.md` | Historical task plan; retain | Distinct plan content; no exact durable replacement established. |
| `prompts/refactoring/REFACTORING_REVIEW.md` | Historical task review; retain | Distinct review content; no exact durable replacement established. |
| `prompts/refactoring/review.md` | Historical task review; retain | Distinct review content; overlap with other reviews is not proven to be duplication. |
| `prompts/slim-backend-full-review.md` | Historical task prompt; retain | Overlapping subject matter is not established as exact duplication; no durable replacement for its distinct instructions. |

## Link and conflict notes

- The old root README refers to `PROJECT_REQUIREMENTS.md` and `XAMPP_DEPLOYMENT.md`; these are inconsistent with the current documentation set and need human link review.
- The new AGENTS/Copilot guidance warns that portions of `rules/project-conventions.mdc` are legacy; after the approved move, retained guidance will point to its archive location.
- Older module specifications are linked by `devdocs/PHASE_EXECUTION_GUIDE.md`. This is a reason to preserve them until inbound links and the intended historical record are reviewed.
- The three ADRs state “Accepted”; whether that status remains current is unresolved.
- Deployment guides, proposal/decision material, review findings, duplicated requirements material, and legacy architecture/schema/API specifications need direct human review before any disposition changes.

## Archive decision and execution

The inventory's initial recommendation was no moves because candidates contain unique information, unresolved status, or active-link concerns. The user directed that all pre-existing files be moved to the archive and not deleted, while keeping newly created files and folders in place. The 59 pre-existing tracked candidates listed in [`_archive/archive-manifest.md`](../../_archive/archive-manifest.md) were moved to their approved destinations; the 15 newly created documentation artifacts remain in their current locations. The user approved using the existing `HEAD` as the pre-move snapshot, with no new commit or staging. Archival was a move only; no candidate was deleted.
