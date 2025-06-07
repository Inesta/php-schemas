<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Renderer\JsonLd;

use Inesta\Schemas\Core\Types\Article;
use Inesta\Schemas\Core\Types\Thing;
use Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer;
use PHPUnit\Framework\TestCase;

use function json_decode;

/**
 * @covers \Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer
 *
 * @internal
 */
final class JsonLdRendererTest extends TestCase
{
    private JsonLdRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->renderer = new JsonLdRenderer();
    }

    public function testGetMimeType(): void
    {
        self::assertSame('application/ld+json', $this->renderer->getMimeType());
    }

    public function testGetFormat(): void
    {
        self::assertSame('json-ld', $this->renderer->getFormat());
    }

    public function testRenderBasicSchema(): void
    {
        $schema = new Thing(['name' => 'Test Thing']);
        $output = $this->renderer->render($schema);

        $decoded = json_decode($output, true);
        self::assertIsArray($decoded);
        self::assertSame('https://schema.org', $decoded['@context']);
        self::assertSame('Thing', $decoded['@type']);
        self::assertSame('Test Thing', $decoded['name']);
    }

    public function testRenderWithCompactOutput(): void
    {
        $schema = new Thing([
            'name' => 'Test Thing',
            'description' => '',
            'url' => null,
        ]);

        $this->renderer->setCompactOutput(true);
        $output = $this->renderer->render($schema);

        $decoded = json_decode($output, true);
        self::assertIsArray($decoded);
        self::assertArrayHasKey('name', $decoded);
        self::assertArrayNotHasKey('description', $decoded);
        self::assertArrayNotHasKey('url', $decoded);
    }

    public function testRenderWithoutPrettyPrint(): void
    {
        $schema = new Thing(['name' => 'Test Thing']);

        $this->renderer->setPrettyPrint(false);
        $output = $this->renderer->render($schema);

        // Compact JSON should not contain newlines
        self::assertStringNotContainsString("\n", $output);
    }

    public function testRenderWithScriptTag(): void
    {
        $schema = new Thing(['name' => 'Test Thing']);

        $this->renderer->setIncludeScriptTag(true);
        $output = $this->renderer->render($schema);

        self::assertStringStartsWith('<script type="application/ld+json">', $output);
        self::assertStringEndsWith('</script>', $output);
        self::assertSame('text/html', $this->renderer->getMimeType());
    }

    public function testRenderWithScriptTagMethod(): void
    {
        $schema = new Thing(['name' => 'Test Thing']);
        $output = $this->renderer->renderWithScriptTag($schema);

        self::assertStringStartsWith('<script type="application/ld+json">', $output);
        self::assertStringEndsWith('</script>', $output);

        // Verify that the original setting is preserved
        self::assertSame('application/ld+json', $this->renderer->getMimeType());
    }

    public function testFluentInterface(): void
    {
        $result = $this->renderer
            ->setPrettyPrint(false)
            ->setUnescapeSlashes(false)
            ->setUnescapeUnicode(false)
            ->setCompactOutput(true)
            ->setIncludeScriptTag(false)
        ;

        self::assertSame($this->renderer, $result);
    }

    public function testEscapingOptions(): void
    {
        $schema = new Thing([
            'name' => 'Test/Thing',
            'description' => 'Test with ñoñó unicode',
            'url' => 'https://example.com/test',
        ]);

        // Test with unescaping enabled (default)
        $output1 = $this->renderer->render($schema);
        self::assertStringContainsString('Test/Thing', $output1);
        self::assertStringContainsString('ñoñó', $output1);

        // Test with unescaping disabled
        $this->renderer
            ->setUnescapeSlashes(false)
            ->setUnescapeUnicode(false)
        ;
        $output2 = $this->renderer->render($schema);
        self::assertStringContainsString('Test\/Thing', $output2);
    }

    public function testRenderComplexArticle(): void
    {
        $article = new Article([
            'headline' => 'Test Article',
            'author' => 'John Doe',
            'description' => 'A test article description',
            'keywords' => ['test', 'article'],
        ]);

        $output = $this->renderer->render($article);
        $decoded = json_decode($output, true);

        self::assertIsArray($decoded);
        self::assertSame('Article', $decoded['@type']);
        self::assertSame('Test Article', $decoded['headline']);
        self::assertSame('John Doe', $decoded['author']);
        self::assertSame(['test', 'article'], $decoded['keywords']);
    }

    public function testRemoveEmptyPropertiesNested(): void
    {
        $schema = new Thing([
            'name' => 'Test',
            'address' => [
                'street' => '',
                'city' => 'New York',
                'country' => null,
            ],
            'contact' => [],
        ]);

        $this->renderer->setCompactOutput(true);
        $output = $this->renderer->render($schema);
        $decoded = json_decode($output, true);

        self::assertIsArray($decoded);
        self::assertArrayNotHasKey('contact', $decoded);
        self::assertArrayHasKey('address', $decoded);
        self::assertIsArray($decoded['address']);
        self::assertArrayNotHasKey('street', $decoded['address']);
        self::assertArrayNotHasKey('country', $decoded['address']);
        self::assertArrayHasKey('city', $decoded['address']);
    }
}
