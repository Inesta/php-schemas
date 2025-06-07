# JSON-LD Article Examples

This document provides comprehensive examples of creating Article schemas using JSON-LD format.

## Basic Article

```php
use Inesta\Schemas\Builder\Builders\ArticleBuilder;
use Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer;

$article = ArticleBuilder::create()
    ->headline('Introduction to Schema.org')
    ->description('Learn how to implement structured data for better SEO.')
    ->author('Jane Smith')
    ->datePublished('2024-01-15')
    ->build();

$renderer = new JsonLdRenderer();
echo $renderer->render($article);
```

**Output:**
```json
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "Introduction to Schema.org",
    "description": "Learn how to implement structured data for better SEO.",
    "author": "Jane Smith",
    "datePublished": "2024-01-15"
}
```

## Article with Person Author

```php
use Inesta\Schemas\Builder\Builders\ArticleBuilder;
use Inesta\Schemas\Builder\Builders\PersonBuilder;

$author = PersonBuilder::create()
    ->name('Dr. Sarah Johnson')
    ->jobTitle('Senior Developer')
    ->email('sarah@example.com')
    ->url('https://sarahjohnson.dev')
    ->build();

$article = ArticleBuilder::create()
    ->headline('Advanced PHP Patterns')
    ->description('Exploring modern PHP design patterns and best practices.')
    ->author($author)
    ->datePublished('2024-02-20')
    ->dateModified('2024-02-25')
    ->keywords(['php', 'design patterns', 'programming'])
    ->wordCount(2500)
    ->build();

echo $article->toJsonLd();
```

**Output:**
```json
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "Advanced PHP Patterns",
    "description": "Exploring modern PHP design patterns and best practices.",
    "author": {
        "@type": "Person",
        "name": "Dr. Sarah Johnson",
        "jobTitle": "Senior Developer",
        "email": "sarah@example.com",
        "url": "https://sarahjohnson.dev"
    },
    "datePublished": "2024-02-20",
    "dateModified": "2024-02-25",
    "keywords": ["php", "design patterns", "programming"],
    "wordCount": 2500
}
```

## Article with Organization Publisher

```php
use Inesta\Schemas\Builder\Builders\ArticleBuilder;
use Inesta\Schemas\Builder\Builders\PersonBuilder;
use Inesta\Schemas\Builder\Builders\OrganizationBuilder;

$author = PersonBuilder::create()
    ->name('Mike Chen')
    ->build();

$publisher = OrganizationBuilder::create()
    ->name('Tech Insights Blog')
    ->url('https://techinsights.example.com')
    ->logo('https://techinsights.example.com/logo.png')
    ->build();

$article = ArticleBuilder::create()
    ->headline('The Future of Web Development')
    ->alternativeHeadline('What\'s Next in Web Tech')
    ->description('Exploring upcoming trends and technologies in web development.')
    ->author($author)
    ->publisher($publisher)
    ->datePublished('2024-03-10')
    ->articleSection('Technology')
    ->image('https://example.com/article-image.jpg')
    ->url('https://techinsights.example.com/future-web-development')
    ->build();
```

**Output:**
```json
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "The Future of Web Development",
    "alternativeHeadline": "What's Next in Web Tech",
    "description": "Exploring upcoming trends and technologies in web development.",
    "author": {
        "@type": "Person",
        "name": "Mike Chen"
    },
    "publisher": {
        "@type": "Organization",
        "name": "Tech Insights Blog",
        "url": "https://techinsights.example.com",
        "logo": "https://techinsights.example.com/logo.png"
    },
    "datePublished": "2024-03-10",
    "articleSection": "Technology",
    "image": "https://example.com/article-image.jpg",
    "url": "https://techinsights.example.com/future-web-development"
}
```

## News Article

```php
use Inesta\Schemas\Builder\Builders\ArticleBuilder;

$newsArticle = ArticleBuilder::create()
    ->headline('Major Tech Conference Announced for 2024')
    ->description('Industry leaders will gather for the largest tech conference of the year.')
    ->author('Reuters News Team')
    ->datePublished('2024-01-08T10:30:00-05:00')
    ->dateModified('2024-01-08T11:15:00-05:00')
    ->articleSection('Technology')
    ->keywords(['technology', 'conference', 'innovation', '2024'])
    ->isAccessibleForFree(true)
    ->inLanguage('en-US')
    ->build();

$renderer = new JsonLdRenderer();
$renderer->setPrettyPrint(true);
echo $renderer->render($newsArticle);
```

## Blog Post with Multiple Authors

```php
use Inesta\Schemas\Builder\Builders\ArticleBuilder;
use Inesta\Schemas\Builder\Builders\PersonBuilder;

$author1 = PersonBuilder::create()
    ->name('Alice Smith')
    ->url('https://alicesmith.dev')
    ->build();

$author2 = PersonBuilder::create()
    ->name('Bob Wilson')
    ->url('https://bobwilson.tech')
    ->build();

$blogPost = ArticleBuilder::create()
    ->headline('Collaborative Development Best Practices')
    ->description('How to effectively collaborate on software projects.')
    ->author([$author1, $author2])
    ->datePublished('2024-01-20')
    ->genre('Technology')
    ->keywords(['collaboration', 'development', 'teamwork'])
    ->mainEntityOfPage('https://blog.example.com/collaborative-development')
    ->build();
```

## Tutorial Article

```php
$tutorial = ArticleBuilder::create()
    ->headline('Step-by-Step Guide to PHP Unit Testing')
    ->description('Complete tutorial on implementing unit tests in PHP applications.')
    ->author('Tutorial Master')
    ->datePublished('2024-02-01')
    ->dateModified('2024-02-15')
    ->articleBody('In this comprehensive tutorial, we will cover...')
    ->educationalLevel('Intermediate')
    ->teaches(['Unit Testing', 'PHPUnit', 'Test-Driven Development'])
    ->timeRequired('PT45M') // 45 minutes in ISO 8601 duration format
    ->keywords(['php', 'testing', 'phpunit', 'tutorial'])
    ->isAccessibleForFree(true)
    ->build();

// Render with script tag for HTML embedding
$renderer = new JsonLdRenderer();
$renderer
    ->setPrettyPrint(true)
    ->setIncludeScriptTag(true);

echo $renderer->render($tutorial);
```

## Compact Output Example

```php
$article = ArticleBuilder::create()
    ->headline('Quick Tips for Developers')
    ->description('')  // Empty description
    ->author('Dev Tips')
    ->url(null)       // Null URL
    ->datePublished('2024-01-30')
    ->keywords([])    // Empty keywords
    ->build();

// Use compact output to remove empty/null properties
$renderer = new JsonLdRenderer();
$renderer
    ->setCompactOutput(true)
    ->setPrettyPrint(false);  // Minified output

echo $renderer->render($article);
```

**Output (compact):**
```json
{"@context":"https://schema.org","@type":"Article","headline":"Quick Tips for Developers","author":"Dev Tips","datePublished":"2024-01-30"}
```

## Custom JSON Flags

```php
$article = ArticleBuilder::create()
    ->headline('Handling Special Characters & Unicode')
    ->description('Article about unicode: ñoñó and slashes: /api/endpoint')
    ->url('https://example.com/articles/unicode-handling')
    ->build();

$renderer = new JsonLdRenderer();
$renderer
    ->setUnescapeSlashes(false)   // Keep slashes escaped
    ->setUnescapeUnicode(false)   // Keep unicode escaped
    ->setPrettyPrint(true);

echo $renderer->render($article);
```

**Output:**
```json
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "Handling Special Characters & Unicode",
    "description": "Article about unicode: \\u00f1o\\u00f1\\u00f3 and slashes: \\/api\\/endpoint",
    "url": "https:\\/\\/example.com\\/articles\\/unicode-handling"
}
```