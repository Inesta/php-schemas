<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Core\Types;

use DateTimeImmutable;
use Inesta\Schemas\Core\Types\Article;
use Inesta\Schemas\Core\Types\Thing;
use PHPUnit\Framework\TestCase;

use function count;

/**
 * @covers \Inesta\Schemas\Core\Types\Article
 *
 * @internal
 */
final class ArticleTest extends TestCase
{
    public function testCanCreateArticleWithMinimalProperties(): void
    {
        $article = new Article([
            'headline' => 'Test Article Headline',
        ]);

        self::assertSame('Article', $article->getType());
        self::assertSame('Test Article Headline', $article->getProperty('headline'));
        self::assertSame('https://schema.org', $article->getContext());
    }

    public function testCanCreateArticleWithComprehensiveProperties(): void
    {
        $datePublished = new DateTimeImmutable('2024-01-01T12:00:00+00:00');
        $dateModified = new DateTimeImmutable('2024-01-02T12:00:00+00:00');

        $article = new Article([
            'headline' => 'Comprehensive Test Article',
            'alternativeHeadline' => 'Alternative Headline',
            'description' => 'A comprehensive test article description',
            'articleBody' => 'The full content of the article goes here...',
            'author' => 'John Doe',
            'publisher' => 'Test Publisher',
            'datePublished' => $datePublished,
            'dateModified' => $dateModified,
            'image' => 'https://example.com/image.jpg',
            'url' => 'https://example.com/article',
            'keywords' => ['test', 'article', 'schema'],
            'wordCount' => 500,
            'articleSection' => 'Technology',
        ]);

        self::assertSame('Article', $article->getType());
        self::assertSame('Comprehensive Test Article', $article->getProperty('headline'));
        self::assertSame('Alternative Headline', $article->getProperty('alternativeHeadline'));
        self::assertSame('A comprehensive test article description', $article->getProperty('description'));
        self::assertSame('The full content of the article goes here...', $article->getProperty('articleBody'));
        self::assertSame('John Doe', $article->getProperty('author'));
        self::assertSame('Test Publisher', $article->getProperty('publisher'));
        self::assertSame($datePublished, $article->getProperty('datePublished'));
        self::assertSame($dateModified, $article->getProperty('dateModified'));
        self::assertSame('https://example.com/image.jpg', $article->getProperty('image'));
        self::assertSame('https://example.com/article', $article->getProperty('url'));
        self::assertSame(['test', 'article', 'schema'], $article->getProperty('keywords'));
        self::assertSame(500, $article->getProperty('wordCount'));
        self::assertSame('Technology', $article->getProperty('articleSection'));
    }

    public function testCanCreateArticleWithCustomContext(): void
    {
        $article = new Article(
            ['headline' => 'Test'],
            'https://custom.context.com',
        );

        self::assertSame('https://custom.context.com', $article->getContext());
    }

    public function testImmutabilityWithPropertyAddition(): void
    {
        $original = new Article(['headline' => 'Original Headline']);
        $modified = $original->withProperty('author', 'Jane Doe');

        self::assertNotSame($original, $modified);
        self::assertNull($original->getProperty('author'));
        self::assertSame('Jane Doe', $modified->getProperty('author'));
        self::assertSame('Original Headline', $original->getProperty('headline'));
        self::assertSame('Original Headline', $modified->getProperty('headline'));
    }

    public function testImmutabilityWithPropertyModification(): void
    {
        $original = new Article([
            'headline' => 'Original Headline',
            'author' => 'Original Author',
        ]);

        $modified = $original->withProperty('headline', 'Modified Headline');

        self::assertNotSame($original, $modified);
        self::assertSame('Original Headline', $original->getProperty('headline'));
        self::assertSame('Modified Headline', $modified->getProperty('headline'));
        self::assertSame('Original Author', $original->getProperty('author'));
        self::assertSame('Original Author', $modified->getProperty('author'));
    }

    public function testHasProperty(): void
    {
        $article = new Article([
            'headline' => 'Test Headline',
            'author' => 'Test Author',
        ]);

        self::assertTrue($article->hasProperty('headline'));
        self::assertTrue($article->hasProperty('author'));
        self::assertFalse($article->hasProperty('nonexistent'));
    }

    public function testToArrayIncludesContextAndType(): void
    {
        $article = new Article([
            'headline' => 'Test Headline',
            'author' => 'Test Author',
        ]);

        $array = $article->toArray();

        self::assertArrayHasKey('@context', $array);
        self::assertArrayHasKey('@type', $array);
        self::assertSame('https://schema.org', $array['@context']);
        self::assertSame('Article', $array['@type']);
        self::assertSame('Test Headline', $array['headline']);
        self::assertSame('Test Author', $array['author']);
    }

    public function testToArrayWithNestedSchemaObject(): void
    {
        $publisher = new Thing(['name' => 'Publisher Name']);
        $article = new Article([
            'headline' => 'Test Headline',
            'publisher' => $publisher,
        ]);

        $array = $article->toArray();

        self::assertIsArray($array['publisher']);
        self::assertSame('Thing', $array['publisher']['@type']);
        self::assertSame('Publisher Name', $array['publisher']['name']);
    }

    public function testToArrayWithDateTime(): void
    {
        $date = new DateTimeImmutable('2024-01-01T12:00:00+00:00');
        $article = new Article([
            'headline' => 'Test Headline',
            'datePublished' => $date,
        ]);

        $array = $article->toArray();

        self::assertSame('2024-01-01T12:00:00+00:00', $array['datePublished']);
    }

    public function testToArrayWithNestedArray(): void
    {
        $image1 = new Thing(['url' => 'https://example.com/image1.jpg']);
        $image2 = new Thing(['url' => 'https://example.com/image2.jpg']);

        $article = new Article([
            'headline' => 'Test Headline',
            'image' => [$image1, $image2],
        ]);

        $array = $article->toArray();

        self::assertIsArray($array['image']);
        self::assertCount(2, $array['image']);
        self::assertSame('Thing', $array['image'][0]['@type']);
        self::assertSame('https://example.com/image1.jpg', $array['image'][0]['url']);
        self::assertSame('Thing', $array['image'][1]['@type']);
        self::assertSame('https://example.com/image2.jpg', $array['image'][1]['url']);
    }

    public function testGetSchemaTypeReturnsCorrectType(): void
    {
        self::assertSame('Article', Article::getSchemaType());
    }

    public function testGetRequiredPropertiesIncludesHeadline(): void
    {
        $required = Article::getRequiredProperties();

        self::assertContains('headline', $required);
        self::assertCount(1, $required);
    }

    public function testGetOptionalPropertiesIncludesCommonProperties(): void
    {
        $optional = Article::getOptionalProperties();

        self::assertContains('author', $optional);
        self::assertContains('publisher', $optional);
        self::assertContains('datePublished', $optional);
        self::assertContains('articleBody', $optional);
        self::assertContains('keywords', $optional);
        self::assertContains('description', $optional);
        self::assertContains('image', $optional);
        self::assertContains('url', $optional);
    }

    public function testGetValidPropertiesIncludesBothRequiredAndOptional(): void
    {
        $valid = Article::getValidProperties();
        $required = Article::getRequiredProperties();
        $optional = Article::getOptionalProperties();

        self::assertContains('headline', $valid);
        self::assertCount(count($required) + count($optional), $valid);

        foreach ($required as $property) {
            self::assertContains($property, $valid);
        }

        foreach ($optional as $property) {
            self::assertContains($property, $valid);
        }
    }

    public function testRendererMethods(): void
    {
        $article = new Article([
            'headline' => 'Test Headline',
            'author' => 'Test Author',
        ]);

        $jsonLd = $article->toJsonLd();
        $microdata = $article->toMicrodata();
        $rdfa = $article->toRdfa();

        self::assertNotEmpty($jsonLd);
        self::assertNotEmpty($microdata);
        self::assertNotEmpty($rdfa);
    }
}
