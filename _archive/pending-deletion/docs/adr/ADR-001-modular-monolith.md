# ADR-001: Modular Monolith Architecture

## Status

Accepted

## Date

2026-07-03

## Context

KCDF Parents is a new platform. The system needs to serve three separate applications (Parent App, Admin Portal, API Backend) and manage multiple business domains (Auth, Families, Academics, Payments, Community, Notifications).

Two architectural approaches were considered:

1. **Microservices from the start** — each domain becomes an independent service
2. **Modular monolith** — single API application, internally divided by business domain

## Decision

Use a **modular monolith** for the initial implementation.

The Slim Framework 4 API backend (`kcdf-api-backend`) is a single application with a well-defined internal module structure:

```
src/Modules/
├── Auth/
├── Families/
├── Academics/
├── Payments/
├── Community/
└── Notifications/
```

Each module is internally self-contained (Controllers, Services, Repositories, Models, Validators, Policies, DTOs) and communicates with other modules only through well-defined service interfaces — not by directly importing each other's Repositories or Models.

## Rationale

- The team is small. Microservices add operational overhead (deployment, inter-service communication, distributed tracing) that is premature at this stage.
- A modular monolith gives the speed and simplicity of a monolith with the clean boundaries needed for future extraction.
- Clean internal module boundaries make it possible to extract any module as a separate Slim application later with minimal refactoring.
- A single MySQL database is sufficient for the initial scale. Read replicas can be added later.

## Consequences

**Positive:**
- Simpler deployment (one PHP application, one database)
- Faster development iteration
- Easier debugging and tracing
- Internal module boundaries are enforced by convention and code review

**Negative:**
- All modules share the same process and database — a bug in one module can affect others
- Scaling a specific domain requires scaling the entire application
- Enforcing module boundaries requires discipline — no automated enforcement at the compiler level

## Future Extraction Path

When a module needs to be extracted:
1. Create a new Slim application
2. Move the module's code verbatim
3. Replace direct service calls with HTTP or event-based calls
4. Add a dedicated database schema for the extracted service
5. Run both in parallel during transition
