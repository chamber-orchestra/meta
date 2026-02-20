# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Symfony package providing a reusable SEO meta data layer — Doctrine ORM trait, enum, and helper methods — designed to mix into content entities.

**Requirements:** PHP ^8.5, Doctrine ORM ^3.0, Symfony ^8.0, `chamber-orchestra/view-bundle` ^8.0

**Namespace:** `ChamberOrchestra\Meta` (PSR-4 from `src/`)

## Commands

```bash
composer install                        # Install dependencies
./bin/phpunit                           # Run all tests
./bin/phpunit --filter ClassName        # Run a specific test class
./bin/phpunit --filter testMethodName   # Run a specific test method
composer test                           # Alias for vendor/bin/phpunit
```

## Architecture

### Entity Layer

- `MetaTrait` — Doctrine ORM trait with mapped properties: `title`, `metaTitle`, `metaImage` (transient, `#[UploadableProperty]`), `metaImagePath`, `metaDescription`, `metaKeywords`, `robotsBehaviour` (smallint with `enumType: RobotsBehaviour`). The `metaImage` File property integrates with file-bundle's upload system via `#[Upload\UploadableProperty(mappedBy: 'metaImagePath')]`.
- `RobotsBehaviour` — int-backed enum: `IndexFollow(0)`, `IndexNoFollow(1)`, `NoIndexFollow(2)`, `NoIndexNoFollow(3)`. Provides `format(): string` for robots meta tag strings.

## Testing

- PHPUnit 13.x; tests in `tests/` autoloaded as `Tests\`
- Unit tests in `tests/Unit/` extend `TestCase`

## Code Conventions

- PSR-12, `declare(strict_types=1)`, 4-space indent
- Typed properties and return types; favor `readonly`
- Constructor injection only; autowiring and autoconfiguration
- Commit style: short, action-oriented with optional bracketed scope — `[fix] ...`, `[master] ...`
