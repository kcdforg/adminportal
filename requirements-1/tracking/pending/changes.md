# Pending Requirement Changes

## Proposed: shared non-functional requirements (awaiting approval)

Source: capability and NFR review, 2026-11-09 (see [review-summary](../../capabilities/review-summary.md)). Nothing below is approved or implemented; existing requirement IDs and text are unchanged.

### New documents (documentation only, already drafted)

- `requirements/capabilities/` — capability catalog, traceability, review summary.
- `requirements/non-functional/` — NFR baseline and audit.

### New requirements — proposed (Part B of [non-functional/index.md](../../non-functional/index.md))

| ID | Change | Open decision |
|---|---|---|
| NFR-SEC-007 | New: login abuse protection | Mechanism and thresholds |
| NFR-SEC-008 | New: consistent password policy | Minimum length/complexity for member and invitation accounts |
| NFR-SEC-009 | New: client token storage decision | Accept `localStorage` or change |
| NFR-SEC-010 | New: HTTPS and security headers | Inspect hosting first |
| NFR-SEC-011 | New: role/permission matrix | Roles and allowed operations |
| NFR-SEC-012 | New: debug disabled in production | Confirm live configuration |
| NFR-PRV-001 | New: minors' personal data, consent, retention | Business and legal policy |
| NFR-PRF-002, NFR-PRF-003 | New: performance targets, index review | Workload and thresholds |
| NFR-REL-004 | New: notification delivery semantics | Channels and retry |
| NFR-AVL-001 | New: availability, backup, RPO/RTO | Objectives |
| NFR-MNT-003 | New: front-end regression tests | Scope |
| NFR-CMP-001 | New: supported browsers/devices | Versions |
| NFR-ACC-001 | New: accessibility baseline | Standard and level |
| NFR-OBS-001, NFR-OBS-004 | New: security-event logging, health monitoring | Retention, health endpoint |

### Existing baseline NFRs for acceptance (Part A)

`NFR-SEC-001` … `NFR-SEC-006`, `NFR-REL-001` … `NFR-REL-003`, `NFR-PRF-001`, `NFR-OBS-002`, `NFR-OBS-003`, `NFR-MNT-001`, `NFR-MNT-002` document verified current behavior; the owner is asked to accept them as baseline.

### Documentation gaps and inconsistencies for decision

See [traceability.md](../../capabilities/traceability.md) sections 3–5: missing member notification and invitation-acceptance requirements, group-content admin/member coverage, duplicated `API-GRP` requirements, and ID gaps. No requirement has been created or changed for these.
