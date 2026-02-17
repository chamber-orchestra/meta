# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Symfony bundle providing a reusable SEO meta data layer — entity interface, Doctrine ORM trait, enum, and optionally CMS form types/DTOs — designed to mix into content entities.

**Requirements:** PHP ^8.5, Doctrine ORM ^3.0, Symfony ^8.0, `chamber-orchestra/file-bundle` ^8.0

**Namespace:** `ChamberOrchestra\MetaBundle` (PSR-4 from package root — no `src/` directory)

**Bundle class:** `ChamberOrchestraMetaBundle` — DI extension is `ChamberOrchestraMetaExtension` (no services registered; entity layer is pure PHP)

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

- `MetaInterface` — contract for `getTitle()`, `getMetaTitle()`, `getMetaDescription()`, `getMetaKeywords()`, `getMetaImage()`, `getMetaImagePath()`, `getRobotsBehaviour()`, `getMeta()`
- `MetaTrait` — Doctrine ORM implementation with mapped properties: `title`, `metaTitle`, `metaImage` (transient, `#[UploadableProperty]`), `metaImagePath`, `metaDescription`, `metaKeywords`, `robotsBehaviour` (smallint with `enumType: RobotsBehaviour`). The `metaImage` File property integrates with file-bundle's upload system via `#[Upload\UploadableProperty(mappedBy: 'metaImagePath')]`.
- `RobotsBehaviour` — int-backed enum: `IndexFollow(0)`, `IndexNoFollow(1)`, `NoIndexFollow(2)`, `NoIndexNoFollow(3)`. Provides `format(): string` for robots meta tag strings.

### CMS/Form Layer (requires external packages)

- `MetaDto` / `MetaType` — admin forms using Symfony `EnumType` for robots behaviour (requires `dev/cms-bundle`, `dev/file-bundle`)
- `MetaTranslatableDto` / `MetaTranslatableType` — multi-language support (requires `dev/translation-bundle`)

## Testing

- PHPUnit 13.x; tests in `tests/` autoloaded as `Tests\`
- Unit tests in `tests/Unit/` extend `TestCase`
- Cms/Form layer excluded from coverage (depends on external packages)

## Code Conventions

- PSR-12, `declare(strict_types=1)`, 4-space indent
- Typed properties and return types; favor `readonly`
- Constructor injection only; autowiring and autoconfiguration
- Commit style: short, action-oriented with optional bracketed scope — `[fix] ...`, `[master] ...`
