<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Renderer\Microdata;

use Inesta\Schemas\Core\Types\Article;
use Inesta\Schemas\Core\Types\Thing;
use Inesta\Schemas\Renderer\Microdata\MicrodataRenderer;
use PHPUnit\Framework\TestCase;

use function mb_substr_count;

/**
 * @covers \Inesta\Schemas\Renderer\Microdata\MicrodataRenderer
 *
 * @internal
 */
final class MicrodataRendererTest extends TestCase
{
    private MicrodataRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->renderer = new MicrodataRenderer();
    }

    public function testGetMimeType(): void
    {
        self::assertSame('text/html', $this->renderer->getMimeType());
    }

    public function testGetFormat(): void
    {
        self::assertSame('microdata', $this->renderer->getFormat());
    }

    public function testRenderBasicSchema(): void
    {
        $schema = new Thing(['name' => 'Test Thing']);
        $output = $this->renderer->render($schema);

        self::assertStringContainsString('itemscope', $output);
        self::assertStringContainsString('itemtype="https://schema.org/Thing"', $output);
        self::assertStringContainsString('itemprop="name"', $output);
        self::assertStringContainsString('Test Thing', $output);
    }

    public function testRenderWithoutPrettyPrint(): void
    {
        $schema = new Thing(['name' => 'Test Thing']);

        $this->renderer->setPrettyPrint(false);
        $output = $this->renderer->render($schema);

        // Compact output should not contain extra newlines
        self::assertStringNotContainsString("\n  ", $output);
    }

    public function testRenderWithSemanticElements(): void
    {
        $article = new Article(['headline' => 'Test Article']);

        $this->renderer->setUseSemanticElements(true);
        $output = $this->renderer->render($article);

        self::assertStringContainsString('<article itemscope', $output);
        self::assertStringContainsString('</article>', $output);
    }

    public function testRenderWithCustomContainerElement(): void
    {
        $schema = new Thing(['name' => 'Test Thing']);

        $this->renderer->setContainerElement('section');
        $output = $this->renderer->render($schema);

        self::assertStringContainsString('<section itemscope', $output);
        self::assertStringContainsString('</section>', $output);
    }

    public function testRenderWithMetaElements(): void
    {
        $article = new Article([
            'headline' => 'Test Article',
            'datePublished' => '2024-01-01',
            'wordCount' => 500,
        ]);

        $this->renderer->setIncludeMetaElements(true);
        $output = $this->renderer->render($article);

        self::assertStringContainsString('<meta itemprop="datePublished"', $output);
        self::assertStringContainsString('<meta itemprop="wordCount"', $output);
        self::assertStringContainsString('content="2024-01-01"', $output);
        self::assertStringContainsString('content="500"', $output);
    }

    public function testRenderWithoutMetaElements(): void
    {
        $article = new Article([
            'headline' => 'Test Article',
            'datePublished' => '2024-01-01',
        ]);

        $this->renderer->setIncludeMetaElements(false);
        $output = $this->renderer->render($article);

        self::assertStringNotContainsString('<meta', $output);
        self::assertStringContainsString('<span itemprop="datePublished"', $output);
    }

    public function testFluentInterface(): void
    {
        $result = $this->renderer
            ->setPrettyPrint(false)
            ->setContainerElement('article')
            ->setUseSemanticElements(true)
            ->setIncludeMetaElements(false)
        ;

        self::assertSame($this->renderer, $result);
    }

    public function testRenderArrayProperty(): void
    {
        $schema = new Thing([
            'name' => 'Test Thing',
            'sameAs' => [
                'https://example1.com',
                'https://example2.com',
            ],
        ]);

        $output = $this->renderer->render($schema);

        self::assertStringContainsString('https://example1.com', $output);
        self::assertStringContainsString('https://example2.com', $output);

        // Should have two separate elements with the same itemprop
        self::assertSame(2, mb_substr_count($output, 'itemprop="sameAs"'));
    }

    public function testRenderNestedSchema(): void
    {
        $author = new Thing(['name' => 'John Doe']);
        $article = new Article([
            'headline' => 'Test Article',
            'author' => $author,
        ]);

        $output = $this->renderer->render($article);

        self::assertStringContainsString('itemprop="author"', $output);
        self::assertStringContainsString('itemscope', $output);
        self::assertStringContainsString('John Doe', $output);
    }

    public function testHtmlEscaping(): void
    {
        $schema = new Thing([
            'name' => 'Test & "Special" <Characters>',
            'description' => 'Text with <script>alert("xss")</script>',
        ]);

        $output = $this->renderer->render($schema);

        self::assertStringContainsString('Test &amp; &quot;Special&quot; &lt;Characters&gt;', $output);
        self::assertStringContainsString('&lt;script&gt;', $output);
        self::assertStringNotContainsString('<script>alert', $output);
    }

    public function testSemanticElementMapping(): void
    {
        $this->renderer->setUseSemanticElements(true);

        // Test Article mapping
        $article = new Article(['headline' => 'Test']);
        $output = $this->renderer->render($article);
        self::assertStringContainsString('<article itemscope', $output);

        // Test Person mapping (should use div)
        $this->renderer = new MicrodataRenderer();
        $this->renderer->setUseSemanticElements(true);
        $person = new Thing(['name' => 'John']); // Thing as Person example
        $output = $this->renderer->render($person);
        self::assertStringContainsString('<div itemscope', $output);
    }

    public function testPropertyElementMapping(): void
    {
        $article = new Article([
            'headline' => 'Main Title',
            'alternativeHeadline' => 'Alt Title',
            'description' => 'Article description',
            'articleBody' => 'Full content here',
        ]);

        $this->renderer->setUseSemanticElements(true);
        $output = $this->renderer->render($article);

        self::assertStringContainsString('<h1 itemprop="headline"', $output);
        self::assertStringContainsString('<h2 itemprop="alternativeHeadline"', $output);
        self::assertStringContainsString('<p itemprop="description"', $output);
        self::assertStringContainsString('<div itemprop="articleBody"', $output);
    }
}
