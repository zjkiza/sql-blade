# Changelog

All notable changes to this project will be documented in this file.

## [0.6.0]
Added
- Support for Laravel 13.x.
- GitHub Actions workflow running the tests on PHP 8.2 - 8.5 with Laravel 11.x - 13.x (prefer-lowest and prefer-stable).

Changed
- The Doctrine DBAL connection is created by `Zjk\SqlBlade\Doctrine\ConnectionFactory` on top of the Laravel PDO, because Laravel 11 removed `Connection::getDoctrineConnection()`.
- Tests no longer depend on the Docker path (`/www`) or on `APP_DEBUG` from `.env`.
- PHPStan, Psalm, PHPMD and PHP CS Fixer are clean on the newer tool versions and run in CI (`static-analysis` job).
- Dev tooling: Psalm ^6 allowed (Psalm 5 does not install on PHP >= 8.4), Orchestra Testbench ^9 - ^11.

Removed
- BREAKING: support for PHP < 8.2 and Laravel < 11 (`composer.json` now requires `php ^8.2`, `illuminate/* ^11.0|^12.0|^13.0`).

## [0.5.6]
Optimization
- Docker optimization for development

## [0.5.5]
Added
- Composer updated.

## [0.5.4]
Added
- Composer updated.
- Continuous Integration in python.

## [0.5.3]
Added
- New functionality query execution via transaction `Zjk\SqlBlade\Contract\SqlBladeInterface->transaction(\Closure $func, int $transactionIsolationLevel = TransactionIsolationLevel::READ_COMMITTED): ?Result`

## [0.5.2]
Added
- Psalm
- Phpmd
- Pdepend

## [0.5.1]
Fixed
- documentation.

## [0.5.0]
Initial version