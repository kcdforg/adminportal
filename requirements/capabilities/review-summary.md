# Capability and NFR Restructuring — Review Summary

## Scope

Documentation-only reorganization of the uploaded requirements ZIP. No source code, DB schema, application requirements, workflows, UI specs or tracking files were changed.

## Results
- Modules: **11**.
- Atomic capability IDs: **40** (CAP-012 through CAP-051).
- Derived requirement/UI mapping rows: **252**; provisional mappings needing closer review: **0**.
- NFR definitions: **30**; existing baseline: **14**; proposed: **16**.
- Original CAP IDs CAP-001…CAP-011 retired, not reused; migration map provided.

## Limitations

- Atomic capability boundaries are a proposed classification derived from the existing module scope and requirement titles. They have not been approved by the user.
- The original source-code evidence is carried forward at module level and does not establish verification of each atomic CAP.
- Shared UI, workflow and NFR references are maintained at module level to avoid falsely assigning them to every CAP.
- The original NFR text retains historical CAP references, explained by the migration map.

## Review priorities

1. Approve or revise individual CAP boundaries, especially cancellation, corrections, locks, and shared authorization.
2. Review every row marked `Mapping requires review`.
3. Confirm whether historical CAP IDs should remain retired and new CAP IDs start at CAP-012.
4. Approve baseline NFRs and proposed NFRs individually; this ZIP makes no approval decisions.

## Links outside the supplied ZIP

Some original references point to `.github/copilot-instructions.md` and `analysis/04-requirements-and-sdlc-review.md`, which were not included in the uploaded requirements ZIP. These references cannot be verified from this archive and were not fabricated.
