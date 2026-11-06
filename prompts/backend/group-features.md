
You are working on api backend in /Users/apple/development/php/kcdf-parents/api-backend-php



# Implement Group Features in KCDF Parents — PHP API, MySQL, and Ionic

## 1. Objective

Implement the group home and group content features in the existing KCDF Parents application.

The application already has a MySQL database, PHP REST API backend, and Ionic frontend. Extend the existing implementation without rebuilding working features or introducing unnecessary dependencies.

Implement:

1. Group home page.
2. Group posts/announcements.
3. Group events and event registration.
4. Group polls, voting, configurable voting rules, and poll results.
5. Group members page.
6. Manage Group page.

Discussion threads, real-time chat, subscription billing, and payment processing are out of scope.

## 2. Mandatory Preliminary Analysis

Inspect the existing repository before modifying code.

Review:
- Database schema and naming conventions.
- Existing group tables, membership tables, foreign keys, and indexes.
- PHP API routes, controllers, services, authentication, authorization, and validation helpers.
- Existing group listing, details, joining, leaving, and member-management functionality.
- Ionic routing, pages, components, API services, forms, and UI conventions.
- Existing user/member identifiers and platform-admin identification.
- Existing transaction, pagination, error-handling, migration, and testing conventions.

Reuse existing functionality wherever possible. Do not duplicate existing APIs or introduce a conflicting authorization model.

Prepare a concise implementation plan, then implement the feature in manageable stages. Do not stop after producing the plan unless a genuine blocker requires clarification.

## 3. API Design — Mandatory Resource-Specific Routes

Use the following API structure:

`/api/v1/groups/{group_id}/posts/...`

`/api/v1/groups/{group_id}/polls/...`

`/api/v1/groups/{group_id}/events/...`

Use separate resource-specific routes for posts, polls, and events, while using the shared database tables described below.

Do not use generic `/groups/{group_id}/contents` endpoints as the primary public API.

Adapt the exact implementation to the existing router, API conventions, and response format.

### 3.1 Posts API

Posts initially represent announcement-style content.

- `GET /api/v1/groups/{group_id}/posts` — list posts.
- `POST /api/v1/groups/{group_id}/posts` — create a post.
- `GET /api/v1/groups/{group_id}/posts/{post_id}` — retrieve a post.
- `PUT /api/v1/groups/{group_id}/posts/{post_id}` — update a post.
- `DELETE /api/v1/groups/{group_id}/posts/{post_id}` — delete or archive a post according to existing conventions.

Support pagination where appropriate.

Validate title, content, status, and all other accepted fields. Derive creator identity from the authenticated server-side context.

Only the `announcement` content type should be exposed through this resource initially.

### 3.2 Polls API

- `GET /api/v1/groups/{group_id}/polls` — list polls.
- `POST /api/v1/groups/{group_id}/polls` — create a poll.
- `GET /api/v1/groups/{group_id}/polls/{poll_id}` — retrieve poll details.
- `PUT /api/v1/groups/{group_id}/polls/{poll_id}` — update a poll, subject to voting and edit restrictions.
- `DELETE /api/v1/groups/{group_id}/polls/{poll_id}` — delete or archive a poll.
- `POST /api/v1/groups/{group_id}/polls/{poll_id}/vote` — submit or update a vote.
- `GET /api/v1/groups/{group_id}/polls/{poll_id}/results` — retrieve results when permitted.

Use `poll_id` as the API resource identifier, mapped internally to `group_contents.id` for a record whose `content_type` is `poll`.

### 3.3 Events API

- `GET /api/v1/groups/{group_id}/events` — list events.
- `POST /api/v1/groups/{group_id}/events` — create an event.
- `GET /api/v1/groups/{group_id}/events/{event_id}` — retrieve event details.
- `PUT /api/v1/groups/{group_id}/events/{event_id}` — update an event.
- `DELETE /api/v1/groups/{group_id}/events/{event_id}` — delete or archive an event.
- `POST /api/v1/groups/{group_id}/events/{event_id}/registrations` — register the current member.
- `GET /api/v1/groups/{group_id}/events/{event_id}/registrations/me` — retrieve the current member's registration.
- `DELETE /api/v1/groups/{group_id}/events/{event_id}/registrations/me` — cancel the current member's registration.

Use `event_id` as the API resource identifier, mapped internally to `group_contents.id` for a record whose `content_type` is `event`.

Restrict access to attendee identities and registration lists according to existing group permissions. Do not expose all attendees to every member unless the application policy explicitly permits it.

### 3.4 Existing Group APIs

Reuse existing group, member, and group-management APIs wherever possible.

Do not create duplicate endpoints for