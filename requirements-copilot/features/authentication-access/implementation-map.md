# Authentication and access — implementation map

Current source is mapped to provisional requirement IDs. **Observed** does not mean tested or approved.

| ID | Backend implementation | Client / schema | Existing test source | Disposition |
|---|---|---|---|---|
| AUTH-001 | `api-backend-php/api/auth/admin-login.php`; `services/identity.php::authenticatePortalLogin`, `issueLoginTokens`; route in `api/routes.php` | Admin `core/services/auth.service.ts`, `features/auth/login/login.component.ts`; `user_logins`, `refresh_tokens` | `tests/phase8-auth-account-types.php` (admin token claims/route registration); `tests/phase1-smoke.php` (route dispatch) | **Observed** |
| AUTH-002 | `api/auth/login.php`; identity authentication/profile lookup/token helpers | Member `core/member-auth.service.ts`, `login/login.page.ts`; `member_profiles`, `user_logins`, `refresh_tokens` | `tests/phase8-auth-account-types.php` is adjacent token/account-type coverage, not an end-to-end member-login test | **Partial**; inactive/suspended profile policy uncertain |
| AUTH-003 | `services/auth.php::extractBearerToken`, `decodeJwtToken`, `checkAuth`; `services/identity.php::getUserRoles`; handler-local auth calls | Admin `core/guards/auth.guard.ts`, `role.guard.ts`, interceptor; member `core/member-auth.service.ts` | `tests/phase8-auth-account-types.php`; `tests/phase7-parity.php` checks auth-header/path routing source | **Observed helper coverage**, incomplete route-level coverage |
| AUTH-004 | `api/auth/refresh.php`; identity token issuance; `refresh_tokens` schema | Admin `core/interceptors/error.interceptor.ts`; member auth service stores token without automatic refresh | No focused refresh-rotation test identified in bounded test inventory | **Partial**; concurrency behavior uncertain |
| AUTH-005 | `api/auth/logout.php`; `refresh_tokens` schema | Admin auth service; no runtime session flow examined | No focused logout-record test identified | **Contradicted**: handler can report success without an eligible row |
| AUTH-006 | `api/admins/{index,store,show,update}.php`; `getAdmin`, identity and activity helpers; `/admins/login-accounts` create alias | No Angular/Ionic admin-account management consumer found; `user_logins`, optional `member_profiles` link | `tests/phase8-auth-account-types.php` has adjacent account-type/role assertions; no focused CRUD/validation test identified | **Observed API-only capability**; no runtime verification |
| AUTH-007 | `api/auth/me.php`; `checkAuth`, `getProfileById`, `resolveLoginDisplayName`, `getUserRoles` | Admin `AuthService.fetchMe()`; no member consumer found; `user_logins`, `member_profiles` | `tests/phase8-auth-account-types.php` has adjacent token/account context assertions; no focused response-contract test identified | **Observed**; runtime/profile response not verified |

## Test evidence boundary

The test files above were read as source only. The source inventory does not imply full acceptance coverage. Phase 05 classifies AUTH-001/AUTH-003 as source-verified, AUTH-002/AUTH-004 partial and AUTH-005 contradicted. No proposed correction is adopted.
