# Microdata Article Examples

This document provides comprehensive examples of creating Article schemas using Microdata format.

## Basic Article

```php
use Inesta\Schemas\Builder\Builders\ArticleBuilder;
use Inesta\Schemas\Renderer\Microdata\MicrodataRenderer;

$article = ArticleBuilder::create()
    ->headline('Introduction to Schema.org')
    ->description('Learn how to implement structured data for better SEO.')
    ->author('Jane Smith')
    ->datePublished('2024-01-15')
    ->build();

$renderer = new MicrodataRenderer();
echo $renderer->render($article);
```

**Output:**
```html
<div itemscope itemtype="https://schema.org/Article">
  <span itemprop="headline">Introduction to Schema.org</span>
  <span itemprop="description">Learn how to implement structured data for better SEO.</span>
  <span itemprop="author">Jane Smith</span>
  <span itemprop="datePublished">2024-01-15</span>
</div>
```

## Article with Semantic HTML Elements

```php
use Inesta\Schemas\Builder\Builders\ArticleBuilder;
use Inesta\Schemas\Builder\Builders\PersonBuilder;

$author = PersonBuilder::create()
    ->name('Dr. Sarah Johnson')
    ->jobTitle('Senior Developer')
    ->email('sarah@example.com')
    ->build();

$article = ArticleBuilder::create()
    ->headline('Advanced PHP Patterns')
    ->alternativeHeadline('Modern PHP Design Patterns')
    ->description('Exploring modern PHP design patterns and best practices.')
    ->articleBody('In this comprehensive guide, we will explore various design patterns that can improve your PHP code quality and maintainability.')
    ->author($author)
    ->datePublished('2024-02-20')
    ->dateModified('2024-02-25')
    ->wordCount(2500)
    ->build();

$renderer = new MicrodataRenderer();
$renderer
    ->setUseSemanticElements(true)
    ->setIncludeMetaElements(true);

echo $renderer->render($article);
```

**Output:**
```html
<article itemscope itemtype="https://schema.org/Article">
  <h1 itemprop="headline">Advanced PHP Patterns</h1>
  <h2 itemprop="alternativeHeadline">Modern PHP Design Patterns</h2>
  <p itemprop="description">Exploring modern PHP design patterns and best practices.</p>
  <div itemprop="articleBody">In this comprehensive guide, we will explore various design patterns that can improve your PHP code quality and maintainability.</div>
  <div itemprop="author" itemscope itemtype="https://schema.org/Person">
    <span itemprop="name">Dr. Sarah Johnson</span>
    <span itemprop="jobTitle">Senior Developer</span>
    <span itemprop="email">sarah@example.com</span>
  </div>
  <meta itemprop="datePublished" content="2024-02-20">
  <meta itemprop="dateModified" content="2024-02-25">
  <meta itemprop="wordCount" content="2500">
</article>
```

## Article with Custom Container Element

```php
$article = ArticleBuilder::create()
    ->headline('Web Development Tips')
    ->description('Essential tips for modern web development.')
    ->author('Web Dev Team')
    ->datePublished('2024-03-01')
    ->build();

$renderer = new MicrodataRenderer();
$renderer
    ->setContainerElement('section')
    ->setPrettyPrint(true);

echo $renderer->render($article);
```

**Output:**
```html
<section itemscope itemtype="https://schema.org/Article">
  <span itemprop="headline">Web Development Tips</span>
  <span itemprop="description">Essential tips for modern web development.</span>
  <span itemprop="author">Web Dev Team</span>
  <span itemprop="datePublished">2024-03-01</span>
</section>
```

## Blog Post with Organization Publisher

```php
use Inesta\Schemas\Builder\Builders\OrganizationBuilder;

$publisher = OrganizationBuilder::create()
    ->name('Tech Insights Blog')
    ->url('https://techinsights.example.com')
    ->logo('https://techinsights.example.com/logo.png')
    ->build();

$blogPost = ArticleBuilder::create()
    ->headline('The Future of Web Development')
    ->description('Exploring upcoming trends and technologies in web development.')
    ->author('Mike Chen')
    ->publisher($publisher)
    ->datePublished('2024-03-10')
    ->articleSection('Technology')
    ->keywords(['web development', 'trends', 'technology'])
    ->url('https://techinsights.example.com/future-web-development')
    ->build();

$renderer = new MicrodataRenderer();
$renderer->setUseSemanticElements(true);
echo $renderer->render($blogPost);
```

**Output:**
```html
<article itemscope itemtype="https://schema.org/Article">
  <h1 itemprop="headline">The Future of Web Development</h1>
  <p itemprop="description">Exploring upcoming trends and technologies in web development.</p>
  <span itemprop="author">Mike Chen</span>
  <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
    <span itemprop="name">Tech Insights Blog</span>
    <span itemprop="url">https://techinsights.example.com</span>
    <span itemprop="logo">https://techinsights.example.com/logo.png</span>
  </div>
  <span itemprop="datePublished">2024-03-10</span>
  <span itemprop="articleSection">Technology</span>
  <span itemprop="keywords">web development</span>
  <span itemprop="keywords">trends</span>
  <span itemprop="keywords">technology</span>
  <span itemprop="url">https://techinsights.example.com/future-web-development</span>
</article>
```

## News Article with Meta Elements

```php
$newsArticle = ArticleBuilder::create()
    ->headline('Breaking: New Technology Breakthrough')
    ->description('Scientists achieve major breakthrough in quantum computing.')
    ->author('Science Reporter')
    ->datePublished('2024-01-08T10:30:00-05:00')
    ->dateModified('2024-01-08T11:15:00-05:00')
    ->wordCount(850)
    ->inLanguage('en-US')
    ->isAccessibleForFree(true)
    ->build();

$renderer = new MicrodataRenderer();
$renderer
    ->setUseSemanticElements(true)
    ->setIncludeMetaElements(true);

echo $renderer->render($newsArticle);
```

**Output:**
```html
<article itemscope itemtype="https://schema.org/Article">
  <h1 itemprop="headline">Breaking: New Technology Breakthrough</h1>
  <p itemprop="description">Scientists achieve major breakthrough in quantum computing.</p>
  <span itemprop="author">Science Reporter</span>
  <meta itemprop="datePublished" content="2024-01-08T10:30:00-05:00">
  <meta itemprop="dateModified" content="2024-01-08T11:15:00-05:00">
  <meta itemprop="wordCount" content="850">
  <span itemprop="inLanguage">en-US</span>
  <span itemprop="isAccessibleForFree">1</span>
</article>
```

## Compact Output Without Meta Elements

```php
$article = ArticleBuilder::create()
    ->headline('Quick Development Tips')
    ->description('Short and useful development tips.')
    ->author('Dev Expert')
    ->datePublished('2024-02-15')
    ->wordCount(300)
    ->build();

$renderer = new MicrodataRenderer();
$renderer
    ->setPrettyPrint(false)
    ->setIncludeMetaElements(false);

echo $renderer->render($article);
```

**Output:**
```html
<div itemscope itemtype="https://schema.org/Article"><span itemprop="headline">Quick Development Tips</span><span itemprop="description">Short and useful development tips.</span><span itemprop="author">Dev Expert</span><span itemprop="datePublished">2024-02-15</span><span itemprop="wordCount">300</span></div>
```

## Tutorial Article with Multiple Authors

```php
$author1 = PersonBuilder::create()
    ->name('Alice Smith')
    ->url('https://alicesmith.dev')
    ->build();

$author2 = PersonBuilder::create()
    ->name('Bob Wilson')
    ->url('https://bobwilson.tech')
    ->build();

$tutorial = ArticleBuilder::create()
    ->headline('Collaborative Development Best Practices')
    ->description('How to effectively collaborate on software projects.')
    ->author([$author1, $author2])
    ->datePublished('2024-01-20')
    ->keywords(['collaboration', 'development', 'teamwork'])
    ->timeRequired('PT45M') // 45 minutes
    ->educationalLevel('Intermediate')
    ->build();

$renderer = new MicrodataRenderer();
$renderer->setUseSemanticElements(true);
echo $renderer->render($tutorial);
```

**Output:**
```html
<article itemscope itemtype="https://schema.org/Article">
  <h1 itemprop="headline">Collaborative Development Best Practices</h1>
  <p itemprop="description">How to effectively collaborate on software projects.</p>
  <div itemprop="author" itemscope itemtype="https://schema.org/Person">
    <span itemprop="name">Alice Smith</span>
    <span itemprop="url">https://alicesmith.dev</span>
  </div>
  <div itemprop="author" itemscope itemtype="https://schema.org/Person">
    <span itemprop="name">Bob Wilson</span>
    <span itemprop="url">https://bobwilson.tech</span>
  </div>
  <span itemprop="datePublished">2024-01-20</span>
  <span itemprop="keywords">collaboration</span>
  <span itemprop="keywords">development</span>
  <span itemprop="keywords">teamwork</span>
  <span itemprop="timeRequired">PT45M</span>
  <span itemprop="educationalLevel">Intermediate</span>
</article>
```

## HTML Escaping Example

```php
$article = ArticleBuilder::create()
    ->headline('Special Characters & "Quotes" <Test>')
    ->description('Content with <script>alert("xss")</script> and other HTML.')
    ->author('Security Expert')
    ->datePublished('2024-03-05')
    ->build();

$renderer = new MicrodataRenderer();
echo $renderer->render($article);
```

**Output:**
```html
<div itemscope itemtype="https://schema.org/Article">
  <span itemprop="headline">Special Characters &amp; &quot;Quotes&quot; &lt;Test&gt;</span>
  <span itemprop="description">Content with &lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt; and other HTML.</span>
  <span itemprop="author">Security Expert</span>
  <span itemprop="datePublished">2024-03-05</span>
</div>
```

## Complex Nested Example

```php
$mainAuthor = PersonBuilder::create()
    ->name('Dr. Emily Johnson')
    ->jobTitle('Lead Researcher')
    ->affiliation(
        OrganizationBuilder::create()
            ->name('University Research Center')
            ->url('https://research.university.edu')
            ->build()
    )
    ->build();

$researchArticle = ArticleBuilder::create()
    ->headline('Advances in Machine Learning Applications')
    ->alternativeHeadline('ML Applications in Modern Software')
    ->description('Comprehensive review of recent advances in machine learning applications across various industries.')
    ->author($mainAuthor)
    ->datePublished('2024-04-01')
    ->dateModified('2024-04-05')
    ->keywords(['machine learning', 'artificial intelligence', 'applications'])
    ->articleSection('Research')
    ->wordCount(4500)
    ->inLanguage('en-US')
    ->isAccessibleForFree(false)
    ->build();

$renderer = new MicrodataRenderer();
$renderer
    ->setUseSemanticElements(true)
    ->setIncludeMetaElements(true)
    ->setPrettyPrint(true);

echo $renderer->render($researchArticle);
```