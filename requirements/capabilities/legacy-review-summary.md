> **Historical document.** CAP-001–CAP-011 references denote the original broad capabilities. See [migration](./id-migration.md).

# Capability and NFR Review Summary

Second-pass review, 2026-11-09 (verifies and reorganizes the first pass). No application file, existing requirement, workflow, UI definition or NFR content was changed.

## Capability summary

| Measure | Value |
|---|---|
| Capabilities | 11 (`CAP-001` … `CAP-011`, IDs and names unchanged) |
| Individual capability files | 11 |
| Requirement headings reviewed | 460 (264 functional, 196 UI), plus 8 workflows and 10 technical documents |
| Mapped (at least one capability) | 442 (264 functional, 178 UI); of these 438 confidently and 4 as **Uncertain** (member Home, `UI-MEM-006-*`) |
| Corrected mappings | 7 requirements re-assigned; 4 workflows and 2 UI screens with corrected relationships (see [traceability.md](./traceability.md) §7) |
| Requirements mapped to multiple capabilities | 73 (31 functional, 42 UI across 10 screens); 6 workflows |
| Unmapped | 18 shared layout/component requirements (intentional, individually reviewed) |
| Cross-cutting | 21 requirements (3 functional gating requirements under CAP-001 + the 18 shared UI) and 6 technical documents |

Counts were recalculated from the repository and match the earlier figures (115 documents, 460 headings, 442/18). The earlier mapping was based mainly on file names; it was re-checked against requirement text and was largely right, with the corrections above.

## Verification summary

- **Statuses:** Implemented — CAP-001, 004, 006, 008, 011; Partially implemented — CAP-002, 003, 005, 007, 009, 010. No capability is "Documented only" or "Proposed".
- **Source evidence:** each capability file cites source paths. Only **30 requirements** were individually checked in code (listed per row in the traceability "Verification" column); the other 430 were content-reviewed for mapping only. Do not read the statuses as full verification.
- **Requirement-to-code discrepancies / observations:** `API-ENR-007` wording vs batch status `upcoming`/`active`; no enrollment, payment or attendance screens in the member app; unused `sample-content.ts`; push/e-mail notification types without delivery. See [traceability.md](./traceability.md) §10–11.
- **Unverified:** per-endpoint authorization, most validation rules, dashboard figures, report correctness, runtime behavior (nothing was executed).

## NFR integration

- Baseline NFRs referenced: `NFR-SEC-001` … `-006`, `NFR-REL-001` … `-003`, `NFR-PRF-001`, `NFR-OBS-002`, `NFR-OBS-003` (and `NFR-MNT-001/002` as system-wide).
- Proposed NFRs referenced (not approved): `NFR-SEC-007`, `-008`, `-009`, `-011`, `NFR-PRV-001`, `NFR-PRF-002`, `-003`, `NFR-REL-004`, `NFR-OBS-001`; system-wide proposed: `NFR-SEC-010`, `-012`, `NFR-AVL-001`, `NFR-MNT-003`, `NFR-CMP-001`, `NFR-ACC-001`, `NFR-OBS-004`.
- Questionable classifications, recorded only: partial NFRs listed in the baseline (`NFR-SEC-003`, `NFR-REL-002`, `NFR-PRF-001`); actor capture in `NFR-OBS-002`. The previous capability index cited `NFR-SEC-005` for payments; that reference was removed because it is a system-wide error-handling NFR.

## Documentation changes

- **Created:** `capabilities/CAP-001-…` through `CAP-011-…` (11 files).
- **Rewritten:** [index.md](./index.md) (now a concise navigation index), [traceability.md](./traceability.md) (requirement-level mapping), this file. Previous content is superseded; its gap and inconsistency findings were carried forward (one earlier claim about admin content management was corrected).
- **Modified:** [../README.md](../README.md), [../../.github/copilot-instructions.md](../../.github/copilot-instructions.md).
- **Intentionally unchanged:** all `applications/`, `workflows/`, `non-functional/`, `tracking/` files, and all application code.

## Issues requiring approval

1. **Boundaries:** member login accounts (CAP-001) vs profiles (CAP-002); sessions in CAP-006 vs batches in CAP-004; enrollment fee state (CAP-005) vs payments (CAP-007); trainers possibly a separate capability.
2. **Uncertain mapping:** member Home screen (`UI-MEM-006`).
3. **Missing requirement documentation:** member notifications, member invitation acceptance, member voting/event registration (UI-only), admin content management (UI-only), entities UI.
4. **ID inconsistencies:** gaps (`API-AUTH-001/002`, `API-PRG-002`, `API-PST-001..004`, `ADM-AUTH-002..006`, `MEM-AUTH-*`, `MEM-PRG-001`), mixed `API-POST`/`API-PST` prefixes, out-of-order IDs, duplicated `API-GRP` requirements.
5. **Source discrepancies:** see Verification summary.
6. **Pending change tracker:** no new entry was added because this pass proposed no new requirements; the earlier pending NFR proposals are unchanged.
