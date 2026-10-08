# Manual library inventory

This directory contains the manually installed third-party libraries used by the plain-PHP backend. The source backend keeps the real dependency list, while this migration keeps the same external dependencies without Composer-based autoloading.

## Included libraries

| Library | Version | Source | License | Notes |
| --- | --- | --- | --- | --- |
| FastRoute | 1.3.0 | https://github.com/nikic/FastRoute | BSD-3-Clause | Used for route registration and dispatch only. |
| Firebase JWT | 7.2.1 | https://github.com/firebase/php-jwt | BSD-3-Clause | JWT parsing and signing helpers. |
| Medoo | 2.6.0 | https://medoo.in/ | MIT | Lightweight database wrapper using the same PDO connection. |
| Monolog | 2.10.0 | https://github.com/Seldaek/monolog | MIT | Logging abstraction during runtime bootstrap and diagnostics. |
| PSR-3 Log | 3.0.2 | https://github.com/php-fig/log | MIT | Shared logging interface. |
| Respect Validation | 2.2.4 | https://github.com/Respect/Validation | Apache-2.0 | Validation library used by later migration phases. |
| Respect Stringifier | 0.2.0 | https://github.com/Respect/Stringifier | MIT | Required by Respect Validation. |
| Symfony Polyfill Mbstring | 1.33.0 | https://github.com/symfony/polyfill-mbstring | MIT | Runtime compatibility for mbstring functions. |

## Notes

- No Composer is used in this project.
- Libraries are loaded by explicit `require_once` and `spl_autoload_register()` in `config/init.php`.
- Each dependency remains under its original package directory and is not rewritten or vendored through a framework.
