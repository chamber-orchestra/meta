# meta-bundle: Detailed Review Patterns

## Bugs & Issues Found (2026-02-17)

### B1 - CI cache key version mismatch (php.yml L35-37)
Cache key uses `php-8.4` but the workflow sets up PHP 8.5. Cache will never hit.

### B2 - AGENTS.md references stale bundle class name
Line 6 says `DevMetaBundle.php` — bundle was renamed to `ChamberOrchestraMetaBundle.php`.

### B3 - MetaTrait implicit symfony/http-foundation dependency
`MetaTrait.php` imports `Symfony\Component\HttpFoundation\File\File` at line 9.
`symfony/http-foundation` is in composer.json `require` so this is technically fine,
but the trait is documented as "standalone entity layer" — tight coupling to File is architectural noise.

### B4 - services.yaml body is empty — no services registered
This may work (Symfony tolerates empty `services:` block) but is misleading.
Should either register form types or be conditional on CMS packages being present.

### B5 - MetaInterface missing getMetaImagePath()
`MetaTrait` implements `getMetaImage(): ?File` and `getMetaImagePath(): ?string`
but `MetaInterface` only declares the 5 core getters (title, metaTitle, metaDescription,
metaKeywords, robotsBehaviour). `getMetaImagePath()` is missing from the interface contract.

### B6 - MetaDto fields not mapped in MetaType
`MetaDto` has `$slug` and `$seoDescription` properties. `MetaType::buildForm()` never
adds form fields for them. These are dead DTO fields or unfinished features.

### B7 - tag.yml auto-tagging drops the `v` prefix on all but the first tag
Line 34: `echo "next=${major}.${minor}.${next_patch}"` - `$major` is captured from
`cut -d. -f1` which would be `v0`, `v1`, etc. On the first release the tag is `v0.0.1`
(hardcoded correctly), but on subsequent releases `major=v1` so the tag becomes `v1.2.4`
(preserving prefix). Actually this is fine, but the cut on line 31-33 makes `$major` include
the `v`, which means the next tag format is correct. This is fine; no bug.

## Code Quality Issues

### Q1 - ORM column type uses legacy string literals
`MetaTrait.php` lines 13, 16, 27: `type: "string"` and `type: 'smallint'`
Should use `Doctrine\DBAL\Types\Types::STRING` and `Types::SMALLINT` for type safety
and IDE navigation. Doctrine deprecated raw string types in ORM 3.x.

### Q2 - RobotsBehaviour::getFormattedBehaviour() should be an instance method
The static method takes `int $behaviour` and internally does `tryFrom()`. Since this
is an enum, this should be an instance method `getFormatted(): string` — callers
pass the enum case, not a raw int. The static form exists to bridge the raw-int
`robotsBehaviour` property in MetaTrait. If MetaTrait stored the enum case directly
this indirection would disappear.

### Q3 - MetaTrait stores int not enum
`protected int $robotsBehaviour` instead of `protected RobotsBehaviour $robotsBehaviour`.
Doctrine ORM 3.x supports PHP enum column mapping natively. This would:
- Make `getRobotsBehaviour()` return the enum type
- Eliminate `getFormattedRobotsBehaviour()` needing to call the static bridge method
- Require changing `MetaInterface::getRobotsBehaviour()` signature

### Q4 - Default robots behaviour is IndexNoFollow(1) — debatable default
New/draft content indexed with nofollow may not be desirable. Most CMS patterns
default to `NoIndexNoFollow` for new records. However this is a design preference.

### Q5 - getMeta() return type is untyped array
`getMeta(): array` could be typed as `getMeta(): array{pageTitle: ?string, title: ?string, image: ?string, description: ?string, keywords: ?string}`
using PHPDoc, or as a typed struct/DTO in PHP 8.5+.

### Q6 - MetaTrait::$metaImage is never persisted
`protected ?File $metaImage = null` is a transient property (no ORM column).
It lives alongside persisted `$metaImagePath`. The `getMetaImage()` getter is in the
trait but there is no corresponding setter in the trait — only in the CMS DTO.
This is a valid upload-handling pattern (file object is transient, path is persisted)
but it should be documented with a comment.

### Q7 - MetaType sets maxlength=127 for title but MetaTrait column is length=255
`MetaType.php` L42-45: maxlength is 127, Length constraint max is 127.
`MetaTrait.php` L13: column length is 255. The DB can hold 255 chars but the
form rejects anything over 127. This inconsistency means data imported or set
programmatically up to 255 chars would pass the DB layer but fail form validation.
Decide on one canonical max (e.g., 255) and align both.

### Q8 - MetaType sets maxlength=127 for metaTitle but column is also 255
Same issue as Q7 for `metaTitle` (L58-62 in MetaType, L16 in MetaTrait).

### Q9 - MetaType metaDescription column is `text` type (unlimited) but form caps at 255
`MetaTrait.php` L24: `type: 'text'` has no DB length limit.
`MetaType.php` L66-69: form caps metaDescription at 255 chars.
`MetaType.php` L71-76: same for metaKeywords but metaKeywords column is varchar(255).
For description this is likely intentional (SEO best practice: ~160 chars), but it
should be consistent. At minimum add a code comment explaining why.

### Q10 - phpunit.xml.dist missing coverage configuration
The `<source>` block excludes `Cms/` and `vendor/` but there is no `<coverage>` element.
Running with `--coverage-html` would still work but there is no `failOnEmptyCoverage`
or minimum threshold. Consider adding `<coverage>` with `processUncoveredFiles="true"`.

## Missing Test Coverage

### T1 - getFormattedRobotsBehaviour() with invalid stored DB value
`testGetFormattedRobotsBehaviourDefault()` and `testGetFormattedRobotsBehaviourCustom()` exist,
but there is no test for what happens when the DB contains a corrupt value (e.g., 99).
`getFormattedRobotsBehaviour()` would throw `OutOfBoundsException` — this should be tested
via `MetaTraitTest`, not just in `RobotsBehaviourTest`.

### T2 - getMeta() strip_tags with complex HTML
`testGetMetaStripsHtmlFromDescription` only tests simple nested tags.
Missing: script tags, HTML entities, multiline HTML, empty-after-strip-tags result.

### T3 - MetaInterface contract test
No test verifies that a class using MetaTrait actually satisfies MetaInterface.
The anonymous class in MetaTraitTest does implement MetaInterface but this is never
asserted explicitly (`self::assertInstanceOf(MetaInterface::class, $entity)`).

### T4 - RobotsBehaviour::choices() order guarantee
`testChoicesReturnsAllCases()` checks count and specific keys but not that the
ordering matches enum declaration order, which matters for form rendering.
