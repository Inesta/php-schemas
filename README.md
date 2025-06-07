# PHP Schema.org Library

[![Latest Version on Packagist](https://img.shields.io/packagist/v/inesta/php-schemas.svg?style=flat-square)](https://packagist.org/packages/inesta/php-schemas)
[![Tests](https://img.shields.io/github/actions/workflow/status/inesta/php-schemas/ci.yml?branch=main&label=tests&style=flat-square)](https://github.com/inesta/php-schemas/actions/workflows/ci.yml)
[![Code Coverage](https://img.shields.io/codecov/c/github/inesta/php-schemas?style=flat-square)](https://codecov.io/gh/inesta/php-schemas)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/inesta/php-schemas.svg?style=flat-square)](https://packagist.org/packages/inesta/php-schemas)
[![PHP Version](https://img.shields.io/packagist/php-v/inesta/php-schemas.svg?style=flat-square)](https://packagist.org/packages/inesta/php-schemas)

A fluent, type-safe PHP library for creating Schema.org structured data. Generate JSON-LD, Microdata, and RDFa markup to improve SEO and enable rich snippets in search results.

## Features

- =€ **Fluent API** - Intuitive, chainable interface for building schemas
- = **Type Safety** - Full PHP 8.3+ type system support with strict typing
-  **Validation** - Built-in Schema.org compliance validation
- <¯ **Multiple Formats** - Support for JSON-LD, Microdata, and RDFa
- >é **Framework Integration** - Ready-to-use Laravel and Symfony adapters
- =Ö **Comprehensive Documentation** - Extensive examples for all schema types
- ¡ **High Performance** - Optimized for production use with caching support
- =á **Testing** - 95%+ code coverage with strict quality standards

## Requirements

- PHP 8.3 or higher
- JSON extension
- Mbstring extension

## Installation

Install the package via Composer:

```bash
composer require inesta/php-schemas
```

## Quick Start

```php
<?php

use Inesta\Schemas\Schema;

// Create an Article schema
$article = Schema::article()
    ->headline('Understanding Schema.org Implementation')
    ->author(
        Schema::person()
            ->name('John Doe')
            ->email('john@example.com')
    )
    ->datePublished(new DateTime('2024-01-15'))
    ->publisher(
        Schema::organization()
            ->name('Tech Blog')
            ->logo('https://example.com/logo.png')
    );

// Output as JSON-LD
echo $article->toJsonLd();

// Output as Microdata
echo $article->toMicrodata();

// Output as RDFa
echo $article->toRdfa();
```

### JSON-LD Output
```json
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "Understanding Schema.org Implementation",
    "author": {
        "@type": "Person",
        "name": "John Doe",
        "email": "john@example.com"
    },
    "datePublished": "2024-01-15",
    "publisher": {
        "@type": "Organization",
        "name": "Tech Blog",
        "logo": "https://example.com/logo.png"
    }
}
```

## Common Use Cases

### E-commerce Product

```php
$product = Schema::product()
    ->name('Premium Headphones')
    ->description('High-quality wireless headphones with noise cancellation')
    ->image('https://example.com/headphones.jpg')
    ->brand(Schema::brand()->name('AudioTech'))
    ->offers(
        Schema::offer()
            ->price('299.99')
            ->priceCurrency('USD')
            ->availability('https://schema.org/InStock')
            ->seller(Schema::organization()->name('Tech Store'))
    )
    ->aggregateRating(
        Schema::aggregateRating()
            ->ratingValue('4.5')
            ->reviewCount(89)
    );
```

### Local Business

```php
$business = Schema::localBusiness()
    ->name('Joe\'s Pizza')
    ->telephone('+1-555-123-4567')
    ->address(
        Schema::postalAddress()
            ->streetAddress('123 Main St')
            ->addressLocality('New York')
            ->addressRegion('NY')
            ->postalCode('10001')
            ->addressCountry('US')
    )
    ->geo(
        Schema::geoCoordinates()
            ->latitude('40.7128')
            ->longitude('-74.0060')
    )
    ->openingHoursSpecification([
        Schema::openingHoursSpecification()
            ->dayOfWeek(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])
            ->opens('11:00')
            ->closes('22:00'),
        Schema::openingHoursSpecification()
            ->dayOfWeek(['Saturday', 'Sunday'])
            ->opens('12:00')
            ->closes('23:00')
    ]);
```

### Event

```php
$event = Schema::event()
    ->name('PHP Conference 2024')
    ->startDate(new DateTime('2024-06-15T09:00:00'))
    ->endDate(new DateTime('2024-06-17T18:00:00'))
    ->location(
        Schema::place()
            ->name('Convention Center')
            ->address(
                Schema::postalAddress()
                    ->streetAddress('456 Expo Blvd')
                    ->addressLocality('San Francisco')
                    ->addressRegion('CA')
            )
    )
    ->offers(
        Schema::offer()
            ->price('199.00')
            ->priceCurrency('USD')
            ->availability('https://schema.org/InStock')
            ->validFrom(new DateTime('2024-01-01'))
    )
    ->organizer(
        Schema::organization()
            ->name('PHP Community')
            ->url('https://phpconf.example.com')
    );
```

## Validation

The library includes comprehensive validation to ensure your schemas are correct:

```php
// Validate before output
$article = Schema::article()->headline('My Article');

// Check if valid
if ($article->isValid()) {
    echo $article->toJsonLd();
} else {
    // Get validation errors
    $errors = $article->getValidationErrors();
    foreach ($errors as $error) {
        echo $error->getMessage();
    }
}

// Or use strict mode (throws exceptions)
Schema::setStrictMode(true);

try {
    $article = Schema::article(); // Missing required properties
    echo $article->toJsonLd(); // Throws ValidationException
} catch (ValidationException $e) {
    echo $e->getMessage();
}
```

## Framework Integration

### Laravel

The package includes auto-discovery for Laravel. Simply install and use:

```php
use Inesta\Schemas\Laravel\Facades\Schema;

// In your Blade templates
{!! Schema::article()->headline($post->title)->toJsonLd() !!}

// In your controllers
return view('article', [
    'schema' => Schema::article()
        ->headline($article->title)
        ->author(Schema::person()->name($article->author->name))
]);
```

### Symfony

Register the bundle in your `config/bundles.php`:

```php
return [
    // ...
    Inesta\Schemas\Symfony\SchemaBundle::class => ['all' => true],
];
```

Use in your templates:

```twig
{{ schema('article', {
    headline: article.title,
    author: schema('person', { name: article.author.name })
}) | json_ld | raw }}
```

## Testing

Run the test suite:

```bash
composer test
```

Run with code coverage:

```bash
composer test:coverage
```

Run static analysis:

```bash
composer analyse
composer psalm
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@inesta.com instead of using the issue tracker.

## Credits

- [Roel Veldhuizen](https://github.com/roelveldhuizen)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.