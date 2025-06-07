# Symfony Integration

This guide shows how to integrate the PHP Schema.org library with Symfony applications.

## Installation

Install the package via Composer:

```bash
composer require inesta/php-schemas
```

## Bundle Registration

Register the bundle in `config/bundles.php`:

```php
<?php

return [
    // ... other bundles
    Inesta\Schemas\Adapters\Symfony\SchemaBundle::class => ['all' => true],
];
```

## Configuration

Create a configuration file at `config/packages/schema.yaml`:

```yaml
schema:
    context: 'https://schema.org'
    default_renderer: 'json_ld'
    
    json_ld:
        pretty_print: true
        include_script_tag: true
        unescape_slashes: true
        unescape_unicode: true
        compact_output: false
    
    microdata:
        pretty_print: true
        use_semantic_elements: true
        include_meta_elements: true
        container_element: 'div'
    
    rdfa:
        pretty_print: true
        use_semantic_elements: true
        include_meta_elements: true
        container_element: 'div'
    
    validation:
        enabled: true
        strict_mode: false
        rules:
            required_properties: true
            property_types: true
            empty_values: true
            schema_org_compliance: true
```

## Basic Usage

### Using the Schema Manager Service

```php
use Inesta\Schemas\Adapters\Symfony\SchemaManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    public function show(int $id, SchemaManager $schemaManager): Response
    {
        $article = $this->getArticle($id);
        
        $schema = $schemaManager->article([
            'headline' => $article->getTitle(),
            'description' => $article->getExcerpt(),
            'author' => $schemaManager->person([
                'name' => $article->getAuthor()->getName(),
                'email' => $article->getAuthor()->getEmail(),
            ]),
            'datePublished' => $article->getPublishedAt()->format('c'),
            'dateModified' => $article->getUpdatedAt()->format('c'),
        ]);

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'schema' => $schema,
        ]);
    }
}
```

### Dependency Injection

The bundle registers these services:

- `Inesta\Schemas\Adapters\Symfony\SchemaManager` (aliased as `schema`)
- `Inesta\Schemas\Builder\Factory\SchemaFactory` (aliased as `schema.factory`)
- `Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer` (aliased as `schema.renderer.json_ld`)
- `Inesta\Schemas\Renderer\Microdata\MicrodataRenderer` (aliased as `schema.renderer.microdata`)
- `Inesta\Schemas\Renderer\Rdfa\RdfaRenderer` (aliased as `schema.renderer.rdfa`)
- `Inesta\Schemas\Validation\ValidationEngine` (aliased as `schema.validator`)

```php
use Inesta\Schemas\Builder\Factory\SchemaFactory;
use Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer;

class SchemaService
{
    public function __construct(
        private SchemaFactory $factory,
        private JsonLdRenderer $renderer,
    ) {}

    public function createProductSchema(Product $product): string
    {
        $schema = $this->factory::create('Product', [
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
        ]);

        return $this->renderer->render($schema);
    }
}
```

## Twig Integration

The bundle provides Twig functions and filters for easy template integration.

### Twig Functions

```twig
{# Create schemas #}
{% set article_schema = schema_article({
    'headline': article.title,
    'description': article.excerpt,
    'author': article.author.name,
    'datePublished': article.publishedAt|date('c')
}) %}

{% set person_schema = schema_person({
    'name': 'John Doe',
    'jobTitle': 'Developer',
    'email': 'john@example.com'
}) %}

{% set org_schema = schema_organization({
    'name': 'Acme Corp',
    'url': 'https://acme.com'
}) %}

{# Generic schema creation #}
{% set product_schema = schema('Product', {
    'name': 'Widget',
    'price': '29.99'
}) %}
```

### Twig Filters

```twig
{# Render as JSON-LD #}
{{ article_schema|json_ld|raw }}

{# Render as Microdata #}
<article>
    {{ article_schema|microdata|raw }}
</article>

{# Render as RDFa #}
<div>
    {{ article_schema|rdfa|raw }}
</div>

{# Use default renderer #}
{{ article_schema|schema_render|raw }}
```

### Complete Template Example

```twig
{# templates/article/show.html.twig #}
{% extends 'base.html.twig' %}

{% block head %}
    {{ parent() }}
    
    {# Create and render article schema as JSON-LD #}
    {% set article_schema = schema_article({
        'headline': article.title,
        'description': article.excerpt,
        'author': schema_person({
            'name': article.author.name,
            'url': path('author_show', {'id': article.author.id})
        }),
        'publisher': schema_organization({
            'name': 'My Blog',
            'url': url('homepage')
        }),
        'datePublished': article.publishedAt|date('c'),
        'dateModified': article.updatedAt|date('c'),
        'image': article.featuredImage,
        'keywords': article.tags|map(tag => tag.name)
    }) %}
    
    {{ article_schema|json_ld|raw }}
{% endblock %}

{% block body %}
    <article>
        <h1>{{ article.title }}</h1>
        
        <div class="article-meta">
            <span>By {{ article.author.name }}</span>
            <time datetime="{{ article.publishedAt|date('c') }}">
                {{ article.publishedAt|date('F j, Y') }}
            </time>
        </div>
        
        <div class="article-content">
            {{ article.content|raw }}
        </div>
        
        {# Alternative: Render as Microdata directly in content #}
        {# {{ article_schema|microdata|raw }} #}
    </article>
{% endblock %}
```

## Advanced Examples

### E-commerce Product Page

```php
class ProductController extends AbstractController
{
    public function show(Product $product, SchemaManager $schema): Response
    {
        $brand = $schema->organization([
            'name' => $product->getBrand(),
        ]);

        $offers = $schema->create('Offer', [
            'price' => $product->getPrice(),
            'priceCurrency' => 'USD',
            'availability' => $product->isInStock() 
                ? 'https://schema.org/InStock' 
                : 'https://schema.org/OutOfStock',
            'seller' => $schema->organization([
                'name' => 'My Store',
            ]),
        ]);

        $productSchema = $schema->create('Product', [
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'image' => $product->getImages(),
            'brand' => $brand,
            'offers' => $offers,
            'sku' => $product->getSku(),
        ]);

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'schema' => $productSchema,
        ]);
    }
}
```

### Event Page

```twig
{% set event_schema = schema('Event', {
    'name': event.title,
    'description': event.description,
    'startDate': event.startDate|date('c'),
    'endDate': event.endDate|date('c'),
    'location': schema('Place', {
        'name': event.venue.name,
        'address': schema('PostalAddress', {
            'streetAddress': event.venue.address,
            'addressLocality': event.venue.city,
            'addressRegion': event.venue.state,
            'postalCode': event.venue.zipCode,
            'addressCountry': event.venue.country
        })
    }),
    'organizer': schema_organization({
        'name': event.organizer.name,
        'url': event.organizer.website
    }),
    'offers': schema('Offer', {
        'price': event.ticketPrice,
        'priceCurrency': 'USD',
        'availability': 'https://schema.org/InStock',
        'validFrom': event.salesStartDate|date('c')
    })
}) %}

{{ event_schema|json_ld|raw }}
```

### Local Business

```php
$business = $schemaManager->create('LocalBusiness', [
    'name' => 'Joe\'s Pizza',
    'description' => 'Best pizza in town',
    'image' => 'https://example.com/pizza-shop.jpg',
    'telephone' => '+1-555-123-4567',
    'address' => $schemaManager->create('PostalAddress', [
        'streetAddress' => '123 Main St',
        'addressLocality' => 'New York',
        'addressRegion' => 'NY',
        'postalCode' => '10001',
        'addressCountry' => 'US',
    ]),
    'geo' => $schemaManager->create('GeoCoordinates', [
        'latitude' => '40.7128',
        'longitude' => '-74.0060',
    ]),
    'openingHoursSpecification' => [
        $schemaManager->create('OpeningHoursSpecification', [
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'opens' => '11:00',
            'closes' => '22:00',
        ]),
        $schemaManager->create('OpeningHoursSpecification', [
            'dayOfWeek' => ['Saturday', 'Sunday'],
            'opens' => '12:00',
            'closes' => '23:00',
        ]),
    ],
]);
```

## Validation

```php
use Inesta\Schemas\Validation\ValidationEngine;

class ArticleService
{
    public function __construct(
        private SchemaManager $schemaManager,
        private ValidationEngine $validator,
    ) {}

    public function createArticleSchema(Article $article): ?string
    {
        $schema = $this->schemaManager->article([
            'headline' => $article->getTitle(),
            'author' => $article->getAuthor()->getName(),
        ]);

        $result = $this->validator->validate($schema);
        
        if (!$result->isValid()) {
            // Log validation errors
            foreach ($result->getErrors() as $error) {
                $this->logger->warning('Schema validation error: ' . $error->getMessage());
            }
            
            // Return null or throw exception based on your needs
            return null;
        }

        return $this->schemaManager->renderJsonLd($schema);
    }
}
```

## Custom Services

### Creating a Custom Schema Service

```php
namespace App\Service;

use Inesta\Schemas\Adapters\Symfony\SchemaManager;
use App\Entity\Article;

class ArticleSchemaService
{
    public function __construct(
        private SchemaManager $schemaManager,
    ) {}

    public function createArticleSchema(Article $article): string
    {
        $author = $this->schemaManager->person([
            'name' => $article->getAuthor()->getName(),
            'url' => $this->generateUrl('author_show', ['id' => $article->getAuthor()->getId()]),
        ]);

        $publisher = $this->schemaManager->organization([
            'name' => 'My Blog',
            'logo' => 'https://myblog.com/logo.png',
            'url' => $this->generateUrl('homepage'),
        ]);

        $schema = $this->schemaManager->article([
            'headline' => $article->getTitle(),
            'description' => $article->getExcerpt(),
            'author' => $author,
            'publisher' => $publisher,
            'datePublished' => $article->getPublishedAt()->format('c'),
            'dateModified' => $article->getUpdatedAt()->format('c'),
            'keywords' => $article->getTags()->map(fn($tag) => $tag->getName())->toArray(),
            'articleSection' => $article->getCategory()->getName(),
            'wordCount' => str_word_count(strip_tags($article->getContent())),
        ]);

        return $this->schemaManager->renderJsonLd($schema);
    }
}
```

Register the service:

```yaml
# config/services.yaml
services:
    App\Service\ArticleSchemaService:
        arguments:
            $schemaManager: '@schema'
```

## Environment-Specific Configuration

### Development Configuration

```yaml
# config/packages/dev/schema.yaml
schema:
    json_ld:
        pretty_print: true
    validation:
        enabled: true
        strict_mode: true
    debug:
        enabled: true
        log_validation_errors: true
```

### Production Configuration

```yaml
# config/packages/prod/schema.yaml
schema:
    json_ld:
        pretty_print: false
        compact_output: true
    validation:
        enabled: false
    debug:
        enabled: false
```

## Testing

### Unit Tests

```php
use Inesta\Schemas\Adapters\Symfony\SchemaManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

class SchemaTest extends TestCase
{
    private SchemaManager $schemaManager;

    protected function setUp(): void
    {
        // Set up container with schema services
        $container = new Container();
        // ... configure container
        
        $this->schemaManager = $container->get(SchemaManager::class);
    }

    public function testArticleSchemaCreation(): void
    {
        $article = $this->schemaManager->article([
            'headline' => 'Test Article',
            'author' => 'Test Author',
        ]);

        $this->assertInstanceOf(SchemaTypeInterface::class, $article);
        $this->assertSame('Test Article', $article->getProperty('headline'));
    }
}
```

### Functional Tests

```php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticlePageTest extends WebTestCase
{
    public function testArticlePageIncludesSchema(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/articles/1');

        $this->assertResponseIsSuccessful();
        
        // Check for JSON-LD script tag
        $scripts = $crawler->filter('script[type="application/ld+json"]');
        $this->assertCount(1, $scripts);
        
        $jsonLd = json_decode($scripts->text(), true);
        $this->assertSame('Article', $jsonLd['@type']);
        $this->assertArrayHasKey('headline', $jsonLd);
    }
}
```

## Best Practices

1. **Use Services**: Create dedicated services for complex schema generation logic.

2. **Environment Configuration**: Use different settings for development vs production.

3. **Validation in Development**: Enable strict validation during development and testing.

4. **Caching**: Consider caching rendered schemas for better performance.

5. **Template Organization**: Keep schema generation in controllers/services, not templates.

6. **Test Your Schemas**: Validate output with Google's Rich Results Test tool.

## Troubleshooting

### Common Issues

1. **Bundle Not Loaded**: Ensure the bundle is registered in `config/bundles.php`.

2. **Services Not Found**: Check that the bundle is properly configured.

3. **Twig Functions Missing**: Verify Twig is installed and the extension is loaded.

4. **Configuration Errors**: Validate your YAML configuration syntax.

5. **Missing Dependencies**: Ensure all required packages are installed.