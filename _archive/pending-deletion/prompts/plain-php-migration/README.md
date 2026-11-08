# Plain PHP API Migration Prompt Pack

Use these prompts in order to migrate `kcdf-api-backend/` from Slim to the
existing plain-PHP project in `api-backend-php/`.

## Prompt order

1. [Phase 0 — Inventory and API contract](phase-0-inventory-and-contract.md)
2. [Phase 1 — Runtime, libraries, and endpoint mapping](phase-1-runtime-and-endpoints.md)
3. [Phase 2 — Authentication and authorization](phase-2-authentication.md)
4. [Phase 3 — Families and identity](phase-3-families-and-identity.md)
5. [Phase 4 — Academics and enrollment](phase-4-academics-and-enrollment.md)
6. [Phase 5 — Payments](phase-5-payments.md)
7. [Phase 6 — Community and notifications](phase-6-community-and-notifications.md)
8. [Phase 7 — Parity, consumer integration, and cutover](phase-7-parity-and-cutover.md)

Run one phase at a time. Each phase prompt is intended to be given to an
implementation agent with access to the repository. Do not begin a later phase
until the current phase's exit checks pass.

## Migration decisions captured here

- The Slim backend is the source of truth; `api-backend-php/` is the destination.
- Keep the existing database schema and data unchanged.
- Do not use Composer, Slim, another PHP framework, or a
  dependency-injection container.
- FastRoute is permitted as a small routing library only. It maps the existing
  HTTP methods and REST-style paths to fixed direct endpoint scripts; it does
  not introduce controllers or a framework.
- Use direct endpoint scripts under `api/`, reusable business logic under
  `services/`, manual third-party source under `libraries/`, and the existing
  `config/` and `storage/` folders.
- Preserve the existing `/api/v1` REST-style URLs. Use FastRoute to map each
  registered method/path to a fixed endpoint file, and a single Apache rewrite
  to send `/api/v1/*` requests to `api/index.php`. This avoids one web-server
  rewrite per endpoint and avoids changing frontend URLs.
- FastRoute v1.3.0 is manually installed under
  `api-backend-php/libraries/fastroute-1.3.0/` with its BSD-3-Clause license.
  Do not use Composer or alter third-party source.
- Treat the existing documented response envelope and business behavior as
  the compatibility baseline unless Phase 0 identifies a deliberate, approved
  API contract change.

The repository's existing `prompts/phase-*.md` documents describe phases for
building the Slim backend; they are not the prompts for this migration.
