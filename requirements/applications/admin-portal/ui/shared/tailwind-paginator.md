# Tailwind Paginator — source-observed share

## Status

This is a working requirement draft for planning and review. It should be checked against live runtime behavior before sign-off.

**Source:** `kcdf-admin-app/src/app/shared/components/paginator/tailwind-paginator.component.html`

**Verification:** Source-only; interaction and runtime not verified.

## UI-ADM-SHR-PAG-001 — Declared interaction 1

The template binds a user interaction to `emitPage(0)`.

## UI-ADM-SHR-PAG-002 — Declared interaction 2

The template binds a user interaction to `emitPage(pageIndex - 1)`.

## UI-ADM-SHR-PAG-003 — Declared interaction 3

The template binds a user interaction to `emitPage(pageIndex + 1)`.

## UI-ADM-SHR-PAG-004 — Declared interaction 4

The template binds a user interaction to `emitPage(pageCount - 1)`.
