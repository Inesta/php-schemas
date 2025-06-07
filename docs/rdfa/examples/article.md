# RDFa Article Examples

This document provides comprehensive examples of creating Article schemas using RDFa format.

## Basic Article

```php
use Inesta\Schemas\Builder\Builders\ArticleBuilder;
use Inesta\Schemas\Renderer\Rdfa\RdfaRenderer;

$article = ArticleBuilder::create()
    ->headline('Introduction to Schema.org')
    ->description('Learn how to implement structured data for better SEO.')
    ->author('Jane Smith')
    ->datePublished('2024-01-15')
    ->build();

$renderer = new RdfaRenderer();
echo $renderer->render($article);
```

**Output:**
```html
<div vocab="https://schema.org/" typeof="Article">
  <span property="headline">Introduction to Schema.org</span>
  <span property="description">Learn how to implement structured data for better SEO.</span>
  <span property="author">Jane Smith</span>
  <span property="datePublished">2024-01-15</span>
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

$renderer = new RdfaRenderer();
$renderer
    ->setUseSemanticElements(true)
    ->setIncludeMetaElements(true);

echo $renderer->render($article);
```

**Output:**
```html
<article vocab="https://schema.org/" typeof="Article">
  <h1 property="headline">Advanced PHP Patterns</h1>
  <h2 property="alternativeHeadline">Modern PHP Design Patterns</h2>
  <p property="description">Exploring modern PHP design patterns and best practices.</p>
  <div property="articleBody">In this comprehensive guide, we will explore various design patterns that can improve your PHP code quality and maintainability.</div>
  <div property="author">
    <div vocab="https://schema.org/" typeof="Person">
      <span property="name">Dr. Sarah Johnson</span>
      <span property="jobTitle">Senior Developer</span>
      <span property="email">sarah@example.com</span>
    </div>
  </div>
  <meta property="datePublished" content="2024-02-20">
  <meta property="dateModified" content="2024-02-25">
  <meta property="wordCount" content="2500">
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

$renderer = new RdfaRenderer();
$renderer
    ->setContainerElement('section')
    ->setPrettyPrint(true);

echo $renderer->render($article);
```

**Output:**
```html
<section vocab="https://schema.org/" typeof="Article">
  <span property="headline">Web Development Tips</span>
  <span property="description">Essential tips for modern web development.</span>
  <span property="author">Web Dev Team</span>
  <span property="datePublished">2024-03-01</span>
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

$renderer = new RdfaRenderer();
$renderer->setUseSemanticElements(true);
echo $renderer->render($blogPost);
```

**Output:**
```html
<article vocab="https://schema.org/" typeof="Article">
  <h1 property="headline">The Future of Web Development</h1>
  <p property="description">Exploring upcoming trends and technologies in web development.</p>
  <span property="author">Mike Chen</span>
  <div property="publisher">
    <div vocab="https://schema.org/" typeof="Organization">
      <span property="name">Tech Insights Blog</span>
      <span property="url">https://techinsights.example.com</span>
      <span property="logo">https://techinsights.example.com/logo.png</span>
    </div>
  </div>
  <span property="datePublished">2024-03-10</span>
  <span property="articleSection">Technology</span>
  <span property="keywords">web development</span>
  <span property="keywords">trends</span>
  <span property="keywords">technology</span>
  <span property="url">https://techinsights.example.com/future-web-development</span>
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

$renderer = new RdfaRenderer();
$renderer
    ->setUseSemanticElements(true)
    ->setIncludeMetaElements(true);

echo $renderer->render($newsArticle);
```

**Output:**
```html
<article vocab="https://schema.org/" typeof="Article">
  <h1 property="headline">Breaking: New Technology Breakthrough</h1>
  <p property="description">Scientists achieve major breakthrough in quantum computing.</p>
  <span property="author">Science Reporter</span>
  <meta property="datePublished" content="2024-01-08T10:30:00-05:00">
  <meta property="dateModified" content="2024-01-08T11:15:00-05:00">
  <meta property="wordCount" content="850">
  <span property="inLanguage">en-US</span>
  <span property="isAccessibleForFree">1</span>
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

$renderer = new RdfaRenderer();
$renderer
    ->setPrettyPrint(false)
    ->setIncludeMetaElements(false);

echo $renderer->render($article);
```

**Output:**
```html
<div vocab="https://schema.org/" typeof="Article"><span property="headline">Quick Development Tips</span><span property="description">Short and useful development tips.</span><span property="author">Dev Expert</span><span property="datePublished">2024-02-15</span><span property="wordCount">300</span></div>
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

$renderer = new RdfaRenderer();
$renderer->setUseSemanticElements(true);
echo $renderer->render($tutorial);
```

**Output:**
```html
<article vocab="https://schema.org/" typeof="Article">
  <h1 property="headline">Collaborative Development Best Practices</h1>
  <p property="description">How to effectively collaborate on software projects.</p>
  <div property="author">
    <div vocab="https://schema.org/" typeof="Person">
      <span property="name">Alice Smith</span>
      <span property="url">https://alicesmith.dev</span>
    </div>
  </div>
  <div property="author">
    <div vocab="https://schema.org/" typeof="Person">
      <span property="name">Bob Wilson</span>
      <span property="url">https://bobwilson.tech</span>
    </div>
  </div>
  <span property="datePublished">2024-01-20</span>
  <span property="keywords">collaboration</span>
  <span property="keywords">development</span>
  <span property="keywords">teamwork</span>
  <span property="timeRequired">PT45M</span>
  <span property="educationalLevel">Intermediate</span>
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

$renderer = new RdfaRenderer();
echo $renderer->render($article);
```

**Output:**
```html
<div vocab="https://schema.org/" typeof="Article">
  <span property="headline">Special Characters &amp; &quot;Quotes&quot; &lt;Test&gt;</span>
  <span property="description">Content with &lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt; and other HTML.</span>
  <span property="author">Security Expert</span>
  <span property="datePublished">2024-03-05</span>
</div>
```

## Research Article with Complex Nested Structure

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

$renderer = new RdfaRenderer();
$renderer
    ->setUseSemanticElements(true)
    ->setIncludeMetaElements(true)
    ->setPrettyPrint(true);

echo $renderer->render($researchArticle);
```

**Output:**
```html
<article vocab="https://schema.org/" typeof="Article">
  <h1 property="headline">Advances in Machine Learning Applications</h1>
  <h2 property="alternativeHeadline">ML Applications in Modern Software</h2>
  <p property="description">Comprehensive review of recent advances in machine learning applications across various industries.</p>
  <div property="author">
    <div vocab="https://schema.org/" typeof="Person">
      <span property="name">Dr. Emily Johnson</span>
      <span property="jobTitle">Lead Researcher</span>
      <div property="affiliation">
        <div vocab="https://schema.org/" typeof="Organization">
          <span property="name">University Research Center</span>
          <span property="url">https://research.university.edu</span>
        </div>
      </div>
    </div>
  </div>
  <meta property="datePublished" content="2024-04-01">
  <meta property="dateModified" content="2024-04-05">
  <span property="keywords">machine learning</span>
  <span property="keywords">artificial intelligence</span>
  <span property="keywords">applications</span>
  <span property="articleSection">Research</span>
  <meta property="wordCount" content="4500">
  <span property="inLanguage">en-US</span>
  <span property="isAccessibleForFree">false</span>
</article>
```

## RDFa with Custom Prefixes

```php
// While this library uses the default Schema.org vocabulary,
// RDFa supports custom prefixes for extended vocabularies

$article = ArticleBuilder::create()
    ->headline('Understanding RDFa Prefixes')
    ->description('How to work with vocabularies and prefixes in RDFa.')
    ->author('RDFa Expert')
    ->datePublished('2024-05-01')
    ->build();

$renderer = new RdfaRenderer();
$renderer->setPrettyPrint(true);

// Note: This example shows standard Schema.org output
// Custom prefixes would require extending the renderer
echo $renderer->render($article);
```