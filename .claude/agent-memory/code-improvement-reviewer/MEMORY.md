# meta-bundle: Code Improvement Reviewer Memory

## Project Overview
- Package: `chamber-orchestra/meta-bundle`
- Namespace: `ChamberOrchestra\MetaBundle` (PSR-4 from root, no src/)
- PHP ^8.5, Doctrine ORM ^3.0, Symfony 8.0
- Bundle class: `ChamberOrchestraMetaBundle`, DI extension: `ChamberOrchestraMetaExtension`

## Key Architectural Notes (post-round-1 fixes)
- Entity layer is standalone (no CMS deps); CMS/Form layer excluded from autoload and coverage
- `MetaTrait` now uses `enumType: RobotsBehaviour::class` on SMALLINT column — native enum mapping
- `MetaTrait::robotsBehaviour` is now `protected RobotsBehaviour` (not raw int)
- `MetaTrait::getRobotsBehaviour()` returns `RobotsBehaviour` enum case (matches MetaInterface contract)
- `MetaInterface` now includes `getMetaImagePath(): ?string` and `getMeta(): array`
- `getRobotsBehaviour(): RobotsBehaviour` returns enum (not raw int)
- `getMeta()` calls `strip_tags()` on description — intentional sanitization
- `robotsBehaviour` defaults to `IndexNoFollow(1)` — deliberately conservative

## Round 1 Issues (FIXED)
- CI cache key version mismatch — fixed (now uses `steps.setup-php.outputs.php-version`)
- AGENTS.md stale bundle class name — fixed
- MetaTrait had stray `File` import — removed
- ORM column types now use `Types::STRING` / `Types::SMALLINT` / `Types::TEXT` constants
- MetaInterface missing `getMetaImagePath()` — added
- MetaDto dead `$slug` / `$seoDescription` fields — removed
- MetaTrait now stores enum (not int)

## Round 2 Issues Found (2026-02-17)
See patterns.md for full detail. Key items:
- `RobotsBehaviour::getFormattedBehaviour(int)` is now dead code — entity stores enum, no caller passes raw int
- `MetaInterface::getMeta()` return type is `array` with no shape documentation
- `composer.json` requires `symfony/http-foundation` for entity-layer but it is only used by CMS DTO (excluded)
- `MetaType` uses a variable `$max` shared across multiple field definitions — confusing
- `MetaType::buildForm()` `metaDescription` uses TextareaType but `attr maxlength` doesn't work on textareas in HTML
- `MetaTranslatableDto` constructor passes `MetaDto::class` hardcoded to `DtoCollection` ignoring translatable-specific DTO
- tag.yml: on the very first run from a clean repo it emits `v0.0.1` correctly; but `$major` from `cut -d. -f1` includes the `v` prefix, so subsequent tags are `v1.2.N` (correct). No bug.
- `services.yaml` body registers no services — misleading but harmless
- `phpunit.xml.dist` has no `<coverage>` element with thresholds
- `CLAUDE.md` line 29 says `getRobotsBehaviour(): int` — stale after round-1 fix (now returns RobotsBehaviour)
- `OutOfBoundsException` / `ExceptionInterface` are only used by `getFormattedBehaviour(int)` — if that's removed, Exception layer becomes dead code
- `MetaDto::$robotsBehaviour` is `?int` but `RobotsBehaviour::choices()` values are also int — no type mismatch, but should be `?RobotsBehaviour` for type-safety
- `MetaInterface::getRobotsBehaviour()` is not in `getMeta()` output — robots tag is not in the returned array
- `MetaTraitTest` missing: test for `getFormattedRobotsBehaviour()` with every case (not just default+custom)
- No test for `getMeta()` including robots behaviour key (because it isn't there — by design or oversight?)

## Testing Patterns
- Tests in `tests/Unit/`, namespace `Tests\Unit\`
- Uses anonymous class implementing `MetaInterface` + `MetaTrait` for entity tests
- PHPUnit 13.x with `#[DataProvider]` attributes
- CMS layer excluded from both autoload and coverage (external deps)

## Links
- Detailed notes: `patterns.md`
