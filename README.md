# ChamberOrchestra Meta Bundle

Reusable SEO meta data layer for Symfony 8 and Doctrine ORM 3. Provides a trait-based approach to add meta title, description, keywords, Open Graph image, and robots behaviour to any Doctrine entity. Includes optional CMS admin form types with translatable (i18n) support.

## Features

- **`MetaInterface`** and **`MetaTrait`** — mix SEO fields into any Doctrine entity with zero boilerplate
- **`RobotsBehaviour`** — int-backed PHP enum with `format()` for robots meta tag output (`index, follow`, `noindex, nofollow`, etc.)
- **`getMeta()`** — returns a clean associative array ready for `<head>` rendering, with automatic HTML stripping on descriptions
- **Native Doctrine enum mapping** — `RobotsBehaviour` is stored as `SMALLINT` with `enumType`, hydrated directly as an enum case
- **File-bundle integration** — transient `File $metaImage` property with `#[UploadableProperty]` for automatic image upload handling via `chamber-orchestra/file-bundle`
- **CMS form types** (optional) — `MetaType` with Symfony `EnumType`, image upload, and validation constraints aligned to column lengths
- **Translatable support** (optional) — `MetaTranslatableType` / `MetaTranslatableDto` for multi-language meta data
- **Translation files** — ships with English and Russian labels for form fields and robots choices

## Requirements

- PHP ^8.5
- Doctrine ORM ^3.0
- Symfony ^8.0
- `chamber-orchestra/file-bundle` ^8.0

Optional (for CMS form layer):

- `chamber-orchestra/cms-bundle`
- `chamber-orchestra/translation-bundle` (for i18n)

## Installation

```bash
composer require chamber-orchestra/meta-bundle
```

## Usage

### 1. Add meta fields to your entity

```php
use ChamberOrchestra\FileBundle\Mapping\Annotation as Upload;
use ChamberOrchestra\MetaBundle\Entity\MetaInterface;
use ChamberOrchestra\MetaBundle\Entity\MetaTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[Upload\Uploadable(prefix: 'article')]
class Article implements MetaInterface
{
    use MetaTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // your entity fields...
}
```

The `#[Uploadable]` class attribute is required for file-bundle to handle the `metaImage` upload automatically.

This adds the following columns and properties to your entity:

| Property           | Type       | Persisted | Description                       |
|--------------------|------------|-----------|-----------------------------------|
| `title`            | `string`   | yes       | Page title (H1)                   |
| `metaTitle`        | `string`   | yes       | `<title>` / og:title              |
| `metaImage`        | `File`     | no        | Transient upload (file-bundle)    |
| `metaImagePath`    | `string`   | yes       | Social share image path           |
| `metaDescription`  | `text`     | yes       | Meta description                  |
| `metaKeywords`     | `string`   | yes       | Meta keywords                     |
| `robotsBehaviour`  | `smallint` | yes       | Robots enum (default: 1)          |

### 2. Render meta tags in Twig

```twig
{% set meta = article.meta %}

<title>{{ meta.title ?? meta.pageTitle }}</title>
<meta name="description" content="{{ meta.description }}">
<meta name="keywords" content="{{ meta.keywords }}">
<meta name="robots" content="{{ article.formattedRobotsBehaviour }}">
{% if meta.image %}
    <meta property="og:image" content="{{ meta.image }}">
{% endif %}
```

The `getMeta()` method automatically strips HTML tags from the description.

### 3. Robots behaviour enum

```php
use ChamberOrchestra\MetaBundle\Entity\Helper\RobotsBehaviour;

$entity->getRobotsBehaviour();              // RobotsBehaviour::IndexNoFollow
$entity->getFormattedRobotsBehaviour();     // "index, nofollow"

RobotsBehaviour::NoIndexNoFollow->format(); // "noindex, nofollow"
```

Available cases:

| Case              | Value | Output               |
|-------------------|-------|----------------------|
| `IndexFollow`     | 0     | `index, follow`      |
| `IndexNoFollow`   | 1     | `index, nofollow`    |
| `NoIndexFollow`   | 2     | `noindex, follow`    |
| `NoIndexNoFollow` | 3     | `noindex, nofollow`  |

### 4. CMS admin forms (optional)

Embed the meta form type in your admin form:

```php
use ChamberOrchestra\MetaBundle\Cms\Form\Type\MetaType;

$builder->add('meta', MetaType::class);
```

For translatable entities:

```php
use ChamberOrchestra\MetaBundle\Cms\Form\Type\MetaTranslatableType;

$builder->add('meta', MetaTranslatableType::class);
```

## Testing

```bash
composer install
composer test
```

## License

Apache-2.0
