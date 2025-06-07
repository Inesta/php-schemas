# PHP Schema.org Library

[![PHP Version](https://img.shields.io/badge/php-%5E8.3-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE.md)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg)](https://phpstan.org/)
[![Tests](https://img.shields.io/badge/tests-passing-brightgreen.svg)](#testing)
[![Code Coverage](https://img.shields.io/badge/coverage-95%25%2B-brightgreen.svg)](#testing)

A modern, type-safe PHP library for creating Schema.org structured data with fluent interfaces and comprehensive validation. Generate JSON-LD, Microdata, and RDFa markup to improve SEO and enable rich snippets.

## Features

### 🛡️ Type Safety First
- Every Schema.org type is a strongly-typed PHP class
- Full PHPStan Level 9 compliance
- Rich IDE autocomplete and error detection

### 🔄 Immutable Objects
- All schema objects are immutable by design
- Modifications return new instances
- Thread-safe and predictable behavior

### 🔧 Fluent Interface
- Builder pattern with chainable methods
- Intuitive and readable API
- IDE-friendly method chaining

### 📊 Multiple Output Formats
- **JSON-LD** - Perfect for search engines and rich snippets
- **Microdata** - Inline HTML markup with schema data
- **RDFa** - Semantic web standard markup

### ✅ Comprehensive Validation
- Built-in Schema.org compliance checking
- Pluggable validation rules system
- Required property validation
- Type safety validation

### 🎯 Framework Integration
- Laravel service provider and facades
- Symfony bundle support
- PSR-4 autoloading compatible

## Installation

Install via Composer:

```bash
composer require inesta/php-schemas
```

### Requirements

- PHP 8.3 or higher
- JSON extension
- mbstring extension (recommended)

## Quick Start

### Basic Usage

```php
use Inesta\Schemas\Builder\Factory\SchemaFactory;

// Create a simple schema
$article = SchemaFactory::create('Article', [
    'headline' => 'How to Use Schema.org in PHP',
    'author' => 'John Doe',
    'datePublished' => '2024-01-15',
    'description' => 'A comprehensive guide to implementing Schema.org in PHP applications.',
]);

// Render as JSON-LD
echo $article->toJsonLd();
```

### Using Builders (Recommended)

```php
use Inesta\Schemas\Builder\Builders\ArticleBuilder;
use Inesta\Schemas\Builder\Builders\PersonBuilder;

// Create a person
$author = PersonBuilder::create()
    ->name('John Doe')
    ->email('john@example.com')
    ->url('https://johndoe.com')
    ->build();

// Create an article with the author
$article = ArticleBuilder::create()
    ->headline('Advanced PHP Techniques')
    ->description('Learn advanced PHP programming techniques.')
    ->author($author)
    ->datePublished('2024-01-15')
    ->keywords(['php', 'programming', 'tutorial'])
    ->build();
```

### Rendering Output

#### JSON-LD (Recommended for SEO)

```php
use Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer;

$renderer = new JsonLdRenderer();
$renderer
    ->setPrettyPrint(true)
    ->setIncludeScriptTag(true);

echo $renderer->render($article);
```

Output:
```html
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "Advanced PHP Techniques",
    "description": "Learn advanced PHP programming techniques.",
    "author": {
        "@type": "Person",
        "name": "John Doe",
        "email": "john@example.com",
        "url": "https://johndoe.com"
    },
    "datePublished": "2024-01-15",
    "keywords": ["php", "programming", "tutorial"]
}
</script>
```

#### Microdata

```php
use Inesta\Schemas\Renderer\Microdata\MicrodataRenderer;

$renderer = new MicrodataRenderer();
$renderer
    ->setUseSemanticElements(true)
    ->setIncludeMetaElements(true);

echo $renderer->render($article);
```

Output:
```html
<article itemscope itemtype="https://schema.org/Article">
  <h1 itemprop="headline">Advanced PHP Techniques</h1>
  <p itemprop="description">Learn advanced PHP programming techniques.</p>
  <div itemprop="author" itemscope itemtype="https://schema.org/Person">
    <span itemprop="name">John Doe</span>
    <span itemprop="email">john@example.com</span>
    <span itemprop="url">https://johndoe.com</span>
  </div>
  <meta itemprop="datePublished" content="2024-01-15">
</article>
```

#### RDFa

```php
use Inesta\Schemas\Renderer\Rdfa\RdfaRenderer;

$renderer = new RdfaRenderer();
$renderer
    ->setUseSemanticElements(true)
    ->setPrettyPrint(true);

echo $renderer->render($article);
```

## Validation

The library includes a comprehensive validation system:

```php
use Inesta\Schemas\Validation\ValidationEngine;
use Inesta\Schemas\Validation\Rules\RequiredPropertiesRule;
use Inesta\Schemas\Validation\Rules\PropertyTypesRule;

$validator = new ValidationEngine();
$validator
    ->addRule(new RequiredPropertiesRule())
    ->addRule(new PropertyTypesRule());

$result = $validator->validate($article);

if (!$result->isValid()) {
    foreach ($result->getErrors() as $error) {
        echo "Error: {$error->getMessage()}\n";
    }
}
```

## Supported Schema Types

Currently supported Schema.org types:

- **Thing** - Base type for all schemas
- **Article** - News articles, blog posts, etc.
- **Person** - Individual people
- **Organization** - Companies, institutions, etc.

More types coming soon! The library is designed to be easily extensible.

## Advanced Usage

### Custom Validation Rules

```php
use Inesta\Schemas\Validation\Interfaces\ValidationRuleInterface;
use Inesta\Schemas\Validation\ValidationResult;

class CustomValidationRule implements ValidationRuleInterface
{
    public function validate(SchemaTypeInterface $schema): ValidationResult
    {
        // Your custom validation logic
        return new ValidationResult(true);
    }

    public function getPriority(): int
    {
        return 100;
    }
}

$validator->addRule(new CustomValidationRule());
```

### Framework Integration

#### Laravel

```php
// config/app.php
'providers' => [
    // ...
    Inesta\Schemas\Adapters\Laravel\SchemaServiceProvider::class,
],

'aliases' => [
    // ...
    'Schema' => Inesta\Schemas\Adapters\Laravel\Facades\Schema::class,
],
```

```php
// Usage in Laravel
$article = Schema::article()
    ->headline('Laravel and Schema.org')
    ->description('Integrating Schema.org with Laravel applications.')
    ->build();
```

#### Symfony

```php
// config/bundles.php
return [
    // ...
    Inesta\Schemas\Adapters\Symfony\SchemaBundle::class => ['all' => true],
];
```

## Testing

Run the test suite:

```bash
# Run all tests
composer test

# Run with coverage
composer test:coverage

# Run specific test suites
composer test:unit
composer test:integration
composer test:compliance
```

## Quality Assurance

This project maintains high code quality standards:

```bash
# Static analysis (PHPStan Level 9)
composer analyse

# Code style (PSR-12)
composer cs:check
composer cs:fix

# All quality checks
composer check-all
```

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`
4. Check code quality: `composer check-all`

## Security

Please review our [Security Policy](SECURITY.md) for reporting vulnerabilities.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and changes.

## License

This project is licensed under the MIT License - see [LICENSE.md](LICENSE.md) for details.

## Acknowledgments

- [Schema.org](https://schema.org/) for the vocabulary specification
- The PHP community for excellent tooling and standards
- All contributors to this project

## Credits

- [Roel Veldhuizen](https://roelveldhuizen.com)
- [Inesta.nl](https://inesta.nl)
- [All Contributors](../../contributors)

---

**Need help?** Check out our [documentation](docs/) or [open an issue](https://github.com/inesta/php-schemas/issues).
