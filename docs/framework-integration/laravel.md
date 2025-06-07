# Laravel Integration

This guide shows how to integrate the PHP Schema.org library with Laravel applications.

## Installation

Install the package via Composer:

```bash
composer require inesta/php-schemas
```

The service provider will be automatically registered via Laravel's package auto-discovery.

## Manual Registration (Optional)

If auto-discovery is disabled, manually register the service provider in `config/app.php`:

```php
'providers' => [
    // ...
    Inesta\Schemas\Adapters\Laravel\SchemaServiceProvider::class,
],

'aliases' => [
    // ...
    'Schema' => Inesta\Schemas\Adapters\Laravel\Facades\Schema::class,
],
```

## Publishing Configuration

Publish the configuration file to customize default settings:

```bash
php artisan vendor:publish --tag=schema-config
```

This creates `config/schema.php` with configurable options for renderers and validation.

## Basic Usage

### Using the Facade

```php
use Inesta\Schemas\Adapters\Laravel\Facades\Schema;

// Create an article
$article = Schema::article([
    'headline' => 'Getting Started with Laravel and Schema.org',
    'description' => 'Learn how to implement structured data in Laravel applications.',
    'author' => 'Laravel Developer',
    'datePublished' => now()->toISOString(),
]);

// Render as JSON-LD with script tag (default)
echo Schema::renderJsonLd($article);

// Render as Microdata
echo Schema::renderMicrodata($article);

// Render as RDFa
echo Schema::renderRdfa($article);
```

### Using Dependency Injection

```php
use Inesta\Schemas\Adapters\Laravel\SchemaManager;

class ArticleController extends Controller
{
    public function show(Article $article, SchemaManager $schema)
    {
        $schemaArticle = $schema->article([
            'headline' => $article->title,
            'description' => $article->excerpt,
            'author' => $schema->person([
                'name' => $article->author->name,
                'email' => $article->author->email,
            ]),
            'datePublished' => $article->published_at->toISOString(),
            'dateModified' => $article->updated_at->toISOString(),
        ]);

        return view('articles.show', [
            'article' => $article,
            'schema' => $schemaArticle,
        ]);
    }
}
```

## Blade Templates

### Using Blade Directives

The package provides convenient Blade directives:

```blade
{{-- JSON-LD with script tag --}}
@jsonld($schema)

{{-- Microdata --}}
@microdata($schema)

{{-- RDFa --}}
@rdfa($schema)

{{-- Generic schema rendering (uses default renderer) --}}
@schema($schema)
```

### Manual Rendering

```blade
{{-- In your layout's <head> section --}}
{!! Schema::renderJsonLd($schema) !!}

{{-- Or in the body for Microdata/RDFa --}}
<article>
    <h1>{{ $article->title }}</h1>
    <div class="content">
        {!! Schema::renderMicrodata($schema, true, true) !!}
    </div>
</article>
```

## Advanced Examples

### Blog Article with Author and Publisher

```php
// In a controller or service
use Inesta\Schemas\Adapters\Laravel\Facades\Schema;

$author = Schema::person([
    'name' => $post->author->name,
    'url' => route('author.show', $post->author),
    'image' => $post->author->avatar_url,
]);

$publisher = Schema::organization([
    'name' => config('app.name'),
    'url' => config('app.url'),
    'logo' => asset('images/logo.png'),
]);

$article = Schema::article([
    'headline' => $post->title,
    'description' => $post->excerpt,
    'image' => $post->featured_image,
    'author' => $author,
    'publisher' => $publisher,
    'datePublished' => $post->published_at->toISOString(),
    'dateModified' => $post->updated_at->toISOString(),
    'keywords' => $post->tags->pluck('name')->toArray(),
    'articleSection' => $post->category->name,
    'wordCount' => str_word_count(strip_tags($post->content)),
]);
```

### Product Page

```php
$product = Schema::create('Product', [
    'name' => $product->name,
    'description' => $product->description,
    'image' => $product->images->map->url->toArray(),
    'brand' => Schema::organization(['name' => $product->brand]),
    'offers' => Schema::create('Offer', [
        'price' => $product->price,
        'priceCurrency' => 'USD',
        'availability' => $product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        'seller' => Schema::organization(['name' => config('app.name')]),
    ]),
    'aggregateRating' => $product->reviews->count() > 0 ? Schema::create('AggregateRating', [
        'ratingValue' => $product->average_rating,
        'reviewCount' => $product->reviews->count(),
    ]) : null,
]);
```

### Local Business

```php
$business = Schema::create('LocalBusiness', [
    'name' => 'Acme Coffee Shop',
    'description' => 'The best coffee in town',
    'address' => Schema::create('PostalAddress', [
        'streetAddress' => '123 Main Street',
        'addressLocality' => 'New York',
        'addressRegion' => 'NY',
        'postalCode' => '10001',
        'addressCountry' => 'US',
    ]),
    'geo' => Schema::create('GeoCoordinates', [
        'latitude' => '40.7128',
        'longitude' => '-74.0060',
    ]),
    'telephone' => '+1-555-123-4567',
    'openingHoursSpecification' => [
        Schema::create('OpeningHoursSpecification', [
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'opens' => '07:00',
            'closes' => '19:00',
        ]),
        Schema::create('OpeningHoursSpecification', [
            'dayOfWeek' => ['Saturday', 'Sunday'],
            'opens' => '08:00',
            'closes' => '17:00',
        ]),
    ],
]);
```

## Validation in Laravel

```php
use Inesta\Schemas\Adapters\Laravel\Facades\Schema;

$article = Schema::article([
    'headline' => 'Test Article',
    // Missing required properties
]);

$result = Schema::validate($article);

if (!$result->isValid()) {
    foreach ($result->getErrors() as $error) {
        Log::warning('Schema validation error: ' . $error->getMessage());
    }
}
```

## Configuration Options

The published configuration file (`config/schema.php`) allows you to customize:

### Default Settings

```php
// config/schema.php
return [
    'default_renderer' => 'json-ld',
    
    'json_ld' => [
        'pretty_print' => true,
        'include_script_tag' => true,
        'unescape_slashes' => true,
        'unescape_unicode' => true,
        'compact_output' => false,
    ],
    
    'microdata' => [
        'pretty_print' => true,
        'use_semantic_elements' => true,
        'include_meta_elements' => true,
        'container_element' => 'div',
    ],
    
    'validation' => [
        'enabled' => true,
        'strict_mode' => false,
    ],
];
```

### Environment Variables

You can also configure via environment variables:

```env
SCHEMA_JSON_LD_PRETTY_PRINT=true
SCHEMA_JSON_LD_SCRIPT_TAG=true
SCHEMA_MICRODATA_SEMANTIC=true
SCHEMA_VALIDATION_ENABLED=true
```

## Service Container Bindings

The package registers these services in Laravel's container:

- `SchemaFactory::class` - Schema factory instance
- `JsonLdRenderer::class` - JSON-LD renderer
- `MicrodataRenderer::class` - Microdata renderer  
- `RdfaRenderer::class` - RDFa renderer
- `ValidationEngine::class` - Validation engine
- `SchemaManager::class` - Main manager (aliased as 'schema')

## Testing

### Unit Testing

```php
use Inesta\Schemas\Adapters\Laravel\Facades\Schema;
use Tests\TestCase;

class SchemaTest extends TestCase
{
    public function test_article_schema_creation()
    {
        $article = Schema::article([
            'headline' => 'Test Article',
            'author' => 'Test Author',
        ]);

        $this->assertInstanceOf(SchemaTypeInterface::class, $article);
        $this->assertSame('Test Article', $article->getProperty('headline'));
    }

    public function test_json_ld_rendering()
    {
        $article = Schema::article(['headline' => 'Test']);
        $jsonLd = Schema::renderJsonLd($article, false, false); // No script tag, no pretty print

        $this->assertStringContainsString('"@type":"Article"', $jsonLd);
        $this->assertStringContainsString('"headline":"Test"', $jsonLd);
    }
}
```

### Feature Testing

```php
use Tests\TestCase;

class ArticlePageTest extends TestCase
{
    public function test_article_page_includes_schema()
    {
        $article = Article::factory()->create([
            'title' => 'Test Article',
            'published_at' => now(),
        ]);

        $response = $this->get(route('articles.show', $article));

        $response->assertStatus(200);
        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('"@type":"Article"', false);
        $response->assertSee('"headline":"Test Article"', false);
    }
}
```

## Best Practices

1. **Create Schemas in Controllers**: Generate schemas in controllers rather than views for better separation of concerns.

2. **Use Validation**: Always validate schemas in development/testing environments.

3. **Cache When Possible**: For expensive schema generation, consider caching the rendered output.

4. **Environment-Specific Configuration**: Use different renderer settings for development vs production.

5. **Test Your Markup**: Use Google's Rich Results Test to validate your structured data.

6. **Consistent Data**: Ensure schema data matches the visible content on your pages.

## Troubleshooting

### Common Issues

1. **Service Provider Not Loaded**: Ensure auto-discovery is enabled or manually register the provider.

2. **Configuration Not Applied**: Make sure you've published and configured `config/schema.php`.

3. **Validation Errors**: Enable debug mode to see detailed validation messages.

4. **Missing Dependencies**: Ensure all required schema properties are provided.