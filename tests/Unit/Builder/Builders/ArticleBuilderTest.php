<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Builder\Builders;

use DateTimeImmutable;
use Inesta\Schemas\Builder\Builders\ArticleBuilder;
use Inesta\Schemas\Core\Types\Article;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Inesta\Schemas\Builder\Builders\ArticleBuilder
 *
 * @internal
 */
final class ArticleBuilderTest extends TestCase
{
    private ArticleBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new ArticleBuilder();
    }

    public function testCanBuildMinimalArticle(): void
    {
        $article = $this->builder
            ->headline('Test Article')
            ->build()
        ;

        self::assertInstanceOf(Article::class, $article);
        self::assertSame('Test Article', $article->getProperty('headline'));
        self::assertSame('https://schema.org', $article->getContext());
    }

    public function testFluentInterface(): void
    {
        $result = $this->builder->headline('Test');

        self::assertSame($this->builder, $result);
    }

    public function testCanBuildComprehensiveArticle(): void
    {
        $datePublished = new DateTimeImmutable('2024-01-01T12:00:00+00:00');
        $dateModified = new DateTimeImmutable('2024-01-02T12:00:00+00:00');

        $article = $this->builder
            ->headline('Comprehensive Test Article')
            ->alternativeHeadline('Alternative Headline')
            ->articleBody('This is the full content of the test article...')
            ->author('John Doe')
            ->publisher('Test Publisher')
            ->datePublished($datePublished)
            ->dateModified($dateModified)
            ->articleSection('Technology')
            ->wordCount(500)
            ->keywords(['test', 'article', 'schema'])
            ->name('Article Name')
            ->description('A comprehensive test article')
            ->url('https://example.com/article')
            ->image('https://example.com/image.jpg')
            ->build()
        ;

        self::assertSame('Comprehensive Test Article', $article->getProperty('headline'));
        self::assertSame('Alternative Headline', $article->getProperty('alternativeHeadline'));
        self::assertSame('This is the full content of the test article...', $article->getProperty('articleBody'));
        self::assertSame('John Doe', $article->getProperty('author'));
        self::assertSame('Test Publisher', $article->getProperty('publisher'));
        self::assertSame($datePublished, $article->getProperty('datePublished'));
        self::assertSame($dateModified, $article->getProperty('dateModified'));
        self::assertSame('Technology', $article->getProperty('articleSection'));
        self::assertSame(500, $article->getProperty('wordCount'));
        self::assertSame(['test', 'article', 'schema'], $article->getProperty('keywords'));
        self::assertSame('Article Name', $article->getProperty('name'));
        self::assertSame('A comprehensive test article', $article->getProperty('description'));
        self::assertSame('https://example.com/article', $article->getProperty('url'));
        self::assertSame('https://example.com/image.jpg', $article->getProperty('image'));
    }

    public function testCanSetKeywordsAsString(): void
    {
        $article = $this->builder
            ->headline('Test Article')
            ->keywords('test,article,schema')
            ->build()
        ;

        self::assertSame('test,article,schema', $article->getProperty('keywords'));
    }

    public function testCanSetImageAsArray(): void
    {
        $images = [
            'https://example.com/image1.jpg',
            'https://example.com/image2.jpg',
        ];

        $article = $this->builder
            ->headline('Test Article')
            ->image($images)
            ->build()
        ;

        self::assertSame($images, $article->getProperty('image'));
    }

    public function testCanSetCustomContext(): void
    {
        $article = $this->builder
            ->setContext('https://custom.context')
            ->headline('Test Article')
            ->build()
        ;

        self::assertSame('https://custom.context', $article->getContext());
    }

    public function testReset(): void
    {
        $this->builder
            ->headline('Test Article')
            ->author('Test Author')
            ->setContext('https://custom.context')
        ;

        $this->builder->reset();

        self::assertSame([], $this->builder->getData());
        self::assertSame('https://schema.org', $this->builder->getContext());
    }

    public function testCanSetComplexAuthor(): void
    {
        $author = [
            '@type' => 'Person',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $article = $this->builder
            ->headline('Test Article')
            ->author($author)
            ->build()
        ;

        self::assertSame($author, $article->getProperty('author'));
    }

    public function testCanSetComplexPublisher(): void
    {
        $publisher = [
            '@type' => 'Organization',
            'name' => 'Example Publishing House',
            'url' => 'https://publisher.example.com',
        ];

        $article = $this->builder
            ->headline('Test Article')
            ->publisher($publisher)
            ->build()
        ;

        self::assertSame($publisher, $article->getProperty('publisher'));
    }
}
