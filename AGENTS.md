# AGENTS.md

## Project Overview

A thin adapter that implements the [PHP/SAP](https://php-sap.github.io) abstract interface
(`php-sap/common`, `php-sap/interfaces`) on top of Gregor Kralik's `ext-sapnwrfc` PHP
extension. It contains almost no business logic of its own: its only job is to translate
between the PHP/SAP generic API (`IConfiguration`, `IApi`, `IFunction`) and the concrete
`\SAPNWRFC\Connection` / `\SAPNWRFC\RemoteFunction` classes provided by the native extension.

## Ecosystem

[PHP/SAP](https://php-sap.github.io) is split across five focused repositories that build on
each other instead of one monolithic package:

| Repository                  | Role                                                                                                    | Depends on (`composer.json`)                                  |
|-----------------------------|---------------------------------------------------------------------------------------------------------|---------------------------------------------------------------|
| `php-sap/interfaces`        | Contract-only interfaces (`IApi`, `IConfiguration`, `IFunction`, exceptions). No concrete classes.      | —                                                             |
| `php-sap/datetime`          | SAP date/time format support on top of native `DateTime`/`DateInterval`.                                | —                                                             |
| `php-sap/common`            | Generic abstract classes, API/config value objects, and exceptions implementing `interfaces`.           | `interfaces`, `datetime`                                      |
| `php-sap/integration-tests` | Shared abstract PHPUnit test infrastructure and SAP module mocks reused by concrete connector packages. | `interfaces`, `common`, `datetime`                            |
| `php-sap/saprfc-kralik`     | Concrete adapter for Gregor Kralik's `ext-sapnwrfc` extension.                                          | `interfaces`, `common` (+ `integration-tests` for tests only) |

**→ You are here: `php-sap/saprfc-kralik`** — the `ext-sapnwrfc` connector.

If you need to change behavior, first check whether it belongs here (extension-specific glue)
or in `php-sap/common` / `php-sap/interfaces` (generic PHP/SAP logic) — those live in
`vendor/php-sap/` and are separate repositories, not owned by this project. Generic PHP/SAP
logic (config mapping rules, API type system, exceptions) belongs upstream, not here.

## Architecture

- `src/SapRfc.php` — the single concrete class, extends `phpsap\classes\AbstractFunction`
  (from `php-sap/common`). Lazily opens a `SAPNWRFC\Connection`, looks up a
  `SAPNWRFC\RemoteFunction` by name, and implements the two abstract methods required by
  the base class: `extractApi()` (introspect the remote function signature) and `invoke()`
  (build params, call, cast results).
- `src/Traits/ConfigTrait.php` — maps a PHP/SAP `IConfiguration` (type A = application
  server, or type B = message server/load balancing) onto the flat array the module's
  `Connection` constructor expects (`ashost`/`sysnr` vs. `mshost`/`group`/`r3name`, plus
  common `client`/`user`/`passwd`/`lang`). Deliberately written in a "stupid", repetitive
  if/return style instead of a generic mapper — see the doc comment in that file, this is
  an intentional style choice, keep new config fields consistent with it.
- `src/Traits/ApiTrait.php` — translates the module's raw `RFCTYPE_*` / `RFC_EXPORT`
  /`RFC_IMPORT`/`RFC_CHANGING`/`RFC_TABLES` vocabulary (as returned by
  `RemoteFunction::getFunctionDescription()`) into PHP/SAP's `IValue`/`IStruct`/`ITable`
  types and `DIRECTION_*` constants via the private `mapType()`/`mapDirection()` lookup
  tables. Unknown SAP types/directions throw `SapLogicException` — extend these tables
  when the module adds new RFC types, don't silently ignore them.
- `src/Traits/ParamTrait.php` — builds the invoke() parameter array from the previously
  set params (only known API input/table elements are forwarded; missing non-optional
  ones throw `FunctionCallException`) and casts the raw invoke() result back through the
  API's `Value`/`Struct`/`Table` objects.
- Note (see `gio/REMARKS.md`): `RFCTYPE_TABLE` is not always paired with direction
  `RFC_TABLES` — some functions return output tables as `RFC_EXPORT`/`RFC_IMPORT`. This is
  a known, currently-unhandled edge case; check that file before "fixing" table handling.

### Testing without a real SAP system

There is no live SAP backend in CI. Tests instead load a hand-written mock of the native
extension's classes:

- `tests/helper/SAPNWRFC.php` is a **copy of the real extension's stub file** (see header
  comment) that redirects every method body to
  `\phpsap\IntegrationTests\SapRfcModuleMocks::singleton()->get(...)`. It self-destructs
  (`die()`) if the real `sapnwrfc` extension is actually loaded, so it never masks a real
  environment.
- Test classes register per-test closures via `static::mock('\SAPNWRFC\Connection::getFunction', function(...) {...})`
  etc. (see `tests/SapRfcIntegrationTest.php`, `tests/OutputTableTest.php`). This lets a
  single test simulate connection success/failure, unknown-function errors, or full
  request/response round trips without a network call.
- The bulk of the test *assertions* live in the shared base classes
  `vendor/php-sap/integration-tests/src/AbstractSapRfcTestCase.php` and
  `AbstractTestCase.php` — this repo only supplies the `mock*()` implementations and
  fixture data (`tests/SapRfcIntegrationTest.php`, `tests/Traits/TestCaseTrait.php`).
  When adding a new scenario, prefer adding a `mock...()` method here over duplicating
  test logic that already exists upstream.
- `tests/config/sap.json` (gitignored, copy from `sap.template.json`) is only used when
  `ext-sapnwrfc` really is loaded, to run the same test suite against a real system.

## Developer Workflows

All commands run through the `Makefile` via Docker, so the host machine does not need a
local PHP installation. Run `make help` for the full target list. Use PHP 8.1, 8.2, and
8.3 (matching the CI matrix in `.github/workflows/php.yml`) for anything
version-sensitive (PHPStan, PHP lint, tests). If you are behind a proxy, `install` and
`audit` already forward `HTTP_PROXY`/`HTTPS_PROXY`/`NO_PROXY`; pass `CA_CERT_FILE=/path/to/ca.pem`
to trust a corporate proxy root CA inside the container.

```bash
# Install/update dependencies for a given PHP version (set DEPENDENCIES_LOWEST=1 for
# --prefer-lowest, matching the CI "lowest" matrix job)
make install PHP_VERSION=8.1

# Run PHPUnit
make test PHP_VERSION=8.1

# Syntax-check every .php file in src/ and tests/, matches CI
make lint PHP_VERSION=8.1

# Run PHPStan
make analyze PHP_VERSION=8.1

# Auto-fix code style (run this before "sniff")
make beautify PHP_VERSION=8.1

# Check code style (uses phpcs.xml)
make sniff PHP_VERSION=8.1

# Check dependencies for known vulnerabilities
make audit

# Run composer validate --strict
make validate
```

**Always use these Makefile targets instead of inventing ad-hoc `docker run`/`composer`/
`php` commands.** If a task needs something the Makefile doesn't expose directly (e.g.
PHPUnit for a single test file/method, or PHPCBF on a single file), take the exact
`docker run` invocation from the matching Makefile target (image, `DOCKER_USER`,
`DOCKER_MOUNT`, env forwarding) and only append the extra PHPUnit/PHPCBF arguments —
don't build the command from scratch. For example, to run a single test class based on
the `test` target:

```bash
docker run --rm -t --init --user "$(id -u)":"$(id -g)" --volume "$(pwd)":/app --workdir /app \
  php:8.1-cli php vendor/bin/phpunit --filter SapRfcIntegrationTest
```

PHPStan runs at **level 5** (`phpstan.neon`, scans `src/`, `tests/`, and `vendor/`) — lower
than the other PHP/SAP packages because this repo introspects the loosely-typed native
extension classes; do not silently raise the level without checking those false positives.

Requires `php: ^8.1` and `ext-sapnwrfc: ^2.1` for production use; the extension itself is
**not** required to run the test suite (see mocking above), but do not add code paths that
would only be exercised with the real extension without a corresponding mock.

## Conventions

- `declare(strict_types=1)` and PHP 8.1 features (readonly-ish typed properties, `match`,
  union return types) are used throughout `src/`; keep new code consistent.
- Catch the module's own exceptions (`SAPNWRFC\ConnectionException`,
  `SAPNWRFC\FunctionCallException`, `TypeError`) at the boundary in `SapRfc.php` and
  re-throw as the corresponding `phpsap\exceptions\*` type — never let `SAPNWRFC\*`
  exceptions escape this package.
- `api/` and `gio/` at the repo root are scratch/reference material (raw API dumps from
  real SAP systems, one-off exploration scripts) — not part of the library's autoloaded
  code and not covered by phpcs/phpstan configs. Don't treat them as source of truth for
  current behavior, but they're useful as real-world API fixture examples.

## Safe Change Strategy for Agents

- Before changing `mapType()`/`mapDirection()` in `ApiTrait.php`, check `gio/REMARKS.md` for
  the known `RFCTYPE_TABLE`/`RFC_EXPORT`/`RFC_IMPORT` edge case so a "fix" doesn't silently
  break the existing handling.
- Before adding a new config field in `ConfigTrait.php`, keep the deliberate repetitive
  if/return style (see the doc comment there) instead of introducing a generic mapper.
- Before adding a new mock scenario, check whether the assertions already exist upstream in
  `vendor/php-sap/integration-tests/src/AbstractSapRfcTestCase.php` — prefer adding a
  `mock...()` method here over duplicating test logic.
- Never let `SAPNWRFC\*` exceptions escape this package; always re-throw as the
  corresponding `phpsap\exceptions\*` type at the `SapRfc.php` boundary.
- Write documentation, comments, and new code in English to match the repository style.
- Always run QA/build commands through the `Makefile` targets, not self-invented `docker run`
  commands. For one-off variants (a single test, a single file), base the invocation on the
  relevant Makefile target and only append the extra arguments.

