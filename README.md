# ChamberOrchestra Meta Bundle

[![PHP Composer](https://github.com/chamber-orchestra/meta-bundle/actions/workflows/php.yml/badge.svg)](https://github.com/chamber-orchestra/meta-bundle/actions/workflows/php.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-max-brightgreen.svg)](https://phpstan.org/)
[![PHP-CS-Fixer](https://img.shields.io/badge/code%20style-PER--CS%20%2F%20Symfony-blue.svg)](https://cs.symfony.com/)
[![Latest Stable Version](https://img.shields.io/packagist/v/chamber-orchestra/meta-bundle.svg)](https://packagist.org/packages/chamber-orchestra/meta-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/chamber-orchestra/meta-bundle.svg)](https://packagist.org/packages/chamber-orchestra/meta-bundle)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP 8.5+](https://img.shields.io/badge/PHP-8.5%2B-777BB4.svg)](https://www.php.net/)
[![Symfony 8.0](https://img.shields.io/badge/Symfony-8.0-000000.svg)](https://symfony.com/)

Symfony 8 bundle providing a Doctrine ORM trait for SEO meta fields — title, description, keywords, Open Graph image, and robots behaviour. Mix into any Doctrine entity with zero boilerplate.

## Features

- **`MetaTrait`** — adds SEO fields to any Doctrine entity via a single `use` statement
- **`RobotsBehaviour`** — int-backed PHP enum with `format()` for robots meta tag output (`index, follow`, `noindex, nofollow`, etc.)
- **`getMeta()`** — returns a clean associative array for rendering, with automatic HTML stripping on descriptions
- **Native Doctrine enum mapping** — `RobotsBehaviour` stored as `SMALLINT` with `enumType`, hydrated directly as an enum case
- **File-bundle integration** — transient `File $metaImage` property with `#[UploadableProperty]` for automatic image upload handling via `chamber-orchestra/file-bundle`

## Requirements

- PHP ^8.5
- Symfony ^8.0
- Doctrine ORM ^3.0
- `chamber-orchestra/view-bundle` ^8.0
- `chamber-orchestra/file-bundle` (in consuming application, for image upload support)

## Installation

```bash
composer require chamber-orchestra/meta-bundle
```

## Usage

### 1. Add meta fields to your entity

```php
use ChamberOrchestra\FileBundle\Mapping\Annotation as Upload;
use ChamberOrchestra\Meta\Entity\MetaTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[Upload\Uploadable(prefix: 'article')]
class Article
{
    use MetaTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // your entity fields...
}
```

The `#[Uploadable]` attribute is required for file-bundle to handle the `metaImage` upload automatically.

This adds the following columns and properties:

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

Pass the `getMeta()` array and the entity to your base layout, then build the `<head>`:

```twig
{# base.html.twig #}
{% set meta = entity.meta %}

{%- set title -%}
    {{ "meta.title.common"|trans }}{{ meta.title|default('') ? ' | ' ~ meta.title }}
    {%- if app.request.query.has("page") and app.request.query.get("page") > 1 -%}
        | {{ "meta.title.page"|trans({"page": app.request.query.get("page")}) -}}
    {%- endif -%}
{%- endset -%}
{%- set title = title|replace({"\n": "", "\r\n": "", "\t": "", "\n\r": ""})|trim -%}
{%- set description = meta.description|default("meta.description.common"|trans) -%}
{%- set keywords = meta.keywords|default("meta.keywords.common"|trans) -%}
{%- set socialTitle = meta.title|default(title) -%}
{%- set socialDescription = meta.description|default(description) -%}

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ title }}</title>

    {%- if
        not app.request.query.has("page")
        and not app.request.query.has("filter")
        and not app.request.query.has("sort")
        and not app.request.query.has("order")
        and not app.request.query.has("limit") -%}
        <meta name="keywords" content="{{ keywords }}">
        <meta name="description" content="{{ description }}">
    {%- endif %}
    <meta name="robots" content="{{ entity.formattedRobotsBehaviour }}">

    <meta property="og:url" content="{{ app.request.uri }}">
    <meta property="og:title" content="{{ socialTitle }}">
    <meta property="og:description" content="{{ socialDescription }}">
    {%- if meta.image %}
        <meta property="og:image" content="{{ absolute_url(meta.image) }}">
    {%- endif %}

    <link rel="canonical" href="{{ app.request.uri }}">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    {% block stylesheets %}{% endblock %}
</head>
```

The `getMeta()` method automatically strips HTML tags from the description.

### 3. Robots behaviour enum

```php
use ChamberOrchestra\Meta\Entity\Helper\RobotsBehaviour;

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

## Testing

```bash
composer install
composer test
composer analyse
composer cs-check
```

## License

MIT
