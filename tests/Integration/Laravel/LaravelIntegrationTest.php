<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Integration\Laravel;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Blade;
use Inesta\Schemas\Adapters\Laravel\Facades\Schema;
use Inesta\Schemas\Adapters\Laravel\SchemaManager;
use Inesta\Schemas\Adapters\Laravel\SchemaServiceProvider;
use Inesta\Schemas\Builder\Factory\SchemaFactory;
use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer;
use Inesta\Schemas\Renderer\Microdata\MicrodataRenderer;
use Inesta\Schemas\Renderer\Rdfa\RdfaRenderer;
use Inesta\Schemas\Validation\ValidationEngine;
use Inesta\Schemas\Validation\ValidationResult;
use Orchestra\Testbench\TestCase;

use function json_decode;

/**
 * Integration tests for Laravel adapter.
 *
 * @internal
 *
 * @coversNothing
 */
final class LaravelIntegrationTest extends TestCase
{
    public function testServiceProviderRegistersServices(): void
    {
        // Test that core services are registered
        self::assertTrue($this->app->bound(SchemaFactory::class));
        self::assertTrue($this->app->bound(SchemaManager::class));
        self::assertTrue($this->app->bound('schema.renderer.json-ld'));
        self::assertTrue($this->app->bound('schema.renderer.microdata'));
        self::assertTrue($this->app->bound('schema.renderer.rdfa'));

        // Test aliases
        self::assertTrue($this->app->bound('schema'));
        self::assertInstanceOf(SchemaManager::class, $this->app->make('schema'));
    }

    public function testFacadeWorks(): void
    {
        $article = Schema::article([
            'headline' => 'Test Article',
            'author' => 'Test Author',
        ]);

        self::assertInstanceOf(SchemaTypeInterface::class, $article);
        self::assertSame('Test Article', $article->getProperty('headline'));
        self::assertSame('Test Author', $article->getProperty('author'));
    }

    public function testSchemaManagerCanCreateSchemas(): void
    {
        /** @var SchemaManager $manager */
        $manager = $this->app->make(SchemaManager::class);

        $person = $manager->person([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        self::assertInstanceOf(SchemaTypeInterface::class, $person);
        self::assertSame('John Doe', $person->getProperty('name'));
        self::assertSame('john@example.com', $person->getProperty('email'));
    }

    public function testSchemaManagerCanRenderSchemas(): void
    {
        /** @var SchemaManager $manager */
        $manager = $this->app->make(SchemaManager::class);

        $article = $manager->article([
            'headline' => 'Laravel Integration Test',
            'author' => 'Test Suite',
        ]);

        // Test JSON-LD rendering
        $jsonLd = $manager->renderJsonLd($article, false, false); // No script tag, no pretty print
        $decoded = json_decode($jsonLd, true);
        self::assertIsArray($decoded);
        self::assertSame('Article', $decoded['@type']);
        self::assertSame('Laravel Integration Test', $decoded['headline']);

        // Test Microdata rendering
        $microdata = $manager->renderMicrodata($article, false, false);
        self::assertStringContainsString('itemscope', $microdata);
        self::assertStringContainsString('itemtype="https://schema.org/Article"', $microdata);

        // Test RDFa rendering
        $rdfa = $manager->renderRdfa($article, false, false);
        self::assertStringContainsString('vocab="https://schema.org/"', $rdfa);
        self::assertStringContainsString('typeof="Article"', $rdfa);
    }

    public function testBladeDirectivesAreRegistered(): void
    {
        // Test that Blade directives are registered
        $directives = Blade::getCustomDirectives();

        self::assertArrayHasKey('schema', $directives);
        self::assertArrayHasKey('jsonld', $directives);
        self::assertArrayHasKey('microdata', $directives);
        self::assertArrayHasKey('rdfa', $directives);
    }

    public function testValidationWorks(): void
    {
        /** @var SchemaManager $manager */
        $manager = $this->app->make(SchemaManager::class);

        // Valid article
        $validArticle = $manager->article([
            'headline' => 'Valid Article',
            'author' => 'Valid Author',
        ]);

        $result = $manager->validate($validArticle);
        self::assertTrue($result->isValid());

        // Invalid article (missing required headline)
        $invalidArticle = $manager->create('Article', [
            'author' => 'Some Author',
            'description' => 'Missing headline',
        ]);

        $result = $manager->validate($invalidArticle);
        self::assertFalse($result->isValid());
        self::assertGreaterThan(0, $result->getErrors());
    }

    public function testRendererConfigurationWorks(): void
    {
        /** @var SchemaManager $manager */
        $manager = $this->app->make(SchemaManager::class);

        $article = $manager->article([
            'headline' => 'Config Test',
            'author' => 'Tester',
        ]);

        // Test JSON-LD with script tag
        $jsonLdWithScript = $manager->renderJsonLd($article, true, true);
        self::assertStringStartsWith('<script type="application/ld+json">', $jsonLdWithScript);
        self::assertStringEndsWith('</script>', $jsonLdWithScript);

        // Test JSON-LD without script tag
        $jsonLdWithoutScript = $manager->renderJsonLd($article, false, false);
        self::assertStringNotContainsString('<script', $jsonLdWithoutScript);
        self::assertStringNotContainsString('</script>', $jsonLdWithoutScript);

        // Test Microdata with semantic elements
        $microdataWithSemantic = $manager->renderMicrodata($article, true, true);
        self::assertStringContainsString('<article itemscope', $microdataWithSemantic);

        // Test Microdata without semantic elements
        $microdataWithoutSemantic = $manager->renderMicrodata($article, false, false);
        self::assertStringContainsString('<div itemscope', $microdataWithoutSemantic);
    }

    public function testComplexSchemaCreation(): void
    {
        /** @var SchemaManager $manager */
        $manager = $this->app->make(SchemaManager::class);

        $author = $manager->person([
            'name' => 'Jane Doe',
            'jobTitle' => 'Writer',
            'email' => 'jane@example.com',
        ]);

        $publisher = $manager->organization([
            'name' => 'Test Publishing',
            'url' => 'https://testpublishing.com',
        ]);

        $article = $manager->article([
            'headline' => 'Complex Article Example',
            'description' => 'An article with nested schemas.',
            'author' => $author,
            'publisher' => $publisher,
            'datePublished' => '2024-01-15',
            'keywords' => ['test', 'integration', 'laravel'],
        ]);

        $jsonLd = $manager->renderJsonLd($article, false, true);
        $decoded = json_decode($jsonLd, true);

        self::assertIsArray($decoded);
        self::assertSame('Article', $decoded['@type']);
        self::assertSame('Complex Article Example', $decoded['headline']);
        self::assertIsArray($decoded['author']);
        self::assertSame('Person', $decoded['author']['@type']);
        self::assertSame('Jane Doe', $decoded['author']['name']);
        self::assertIsArray($decoded['publisher']);
        self::assertSame('Organization', $decoded['publisher']['@type']);
        self::assertSame('Test Publishing', $decoded['publisher']['name']);
        self::assertSame(['test', 'integration', 'laravel'], $decoded['keywords']);
    }

    public function testServiceResolutionThroughContainer(): void
    {
        // Test that we can resolve services through the container
        $factory = $this->app->make(SchemaFactory::class);
        self::assertInstanceOf(SchemaFactory::class, $factory);

        $jsonLdRenderer = $this->app->make('schema.renderer.json-ld');
        self::assertInstanceOf(JsonLdRenderer::class, $jsonLdRenderer);

        $microdataRenderer = $this->app->make('schema.renderer.microdata');
        self::assertInstanceOf(MicrodataRenderer::class, $microdataRenderer);

        $rdfaRenderer = $this->app->make('schema.renderer.rdfa');
        self::assertInstanceOf(RdfaRenderer::class, $rdfaRenderer);

        $validationEngine = $this->app->make(ValidationEngine::class);
        self::assertInstanceOf(ValidationEngine::class, $validationEngine);
    }

    public function testFacadeCanAccessAllMethods(): void
    {
        // Test all main facade methods work
        $article = Schema::article(['headline' => 'Facade Test', 'author' => 'Tester']);
        $person = Schema::person(['name' => 'Test Person']);
        $org = Schema::organization(['name' => 'Test Org']);
        $thing = Schema::thing(['name' => 'Test Thing']);

        self::assertInstanceOf(SchemaTypeInterface::class, $article);
        self::assertInstanceOf(SchemaTypeInterface::class, $person);
        self::assertInstanceOf(SchemaTypeInterface::class, $org);
        self::assertInstanceOf(SchemaTypeInterface::class, $thing);

        // Test rendering methods
        $jsonLd = Schema::renderJsonLd($article);
        $microdata = Schema::renderMicrodata($article);
        $rdfa = Schema::renderRdfa($article);
        $default = Schema::render($article);

        self::assertIsString($jsonLd);
        self::assertIsString($microdata);
        self::assertIsString($rdfa);
        self::assertIsString($default);

        // Test validation
        $result = Schema::validate($article);
        self::assertInstanceOf(ValidationResult::class, $result);
    }

    /**
     * Get package providers.
     *
     * @param Application $app
     *
     * @return array<string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            SchemaServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param Application $app
     *
     * @return array<string, string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Schema' => Schema::class,
        ];
    }
}
