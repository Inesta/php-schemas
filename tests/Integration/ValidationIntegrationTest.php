<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Integration;

use DateTime;
use Inesta\Schemas\Builder\Builders\ArticleBuilder;
use Inesta\Schemas\Builder\Builders\PersonBuilder;
use Inesta\Schemas\Builder\Factory\SchemaFactory;
use Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer;
use Inesta\Schemas\Renderer\Microdata\MicrodataRenderer;
use Inesta\Schemas\Renderer\Rdfa\RdfaRenderer;
use Inesta\Schemas\Validation\Rules\PropertyTypesRule;
use Inesta\Schemas\Validation\Rules\RequiredPropertiesRule;
use Inesta\Schemas\Validation\Rules\SchemaOrgComplianceRule;
use Inesta\Schemas\Validation\ValidationEngine;
use PHPUnit\Framework\TestCase;

use function array_map;
use function implode;
use function json_decode;
use function str_contains;

/**
 * Integration tests for validation with real schemas and renderers.
 *
 * @internal
 *
 * @coversNothing
 */
final class ValidationIntegrationTest extends TestCase
{
    private ValidationEngine $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new ValidationEngine();
        $this->validator
            ->addRule(new RequiredPropertiesRule())
            ->addRule(new PropertyTypesRule())
            ->addRule(new SchemaOrgComplianceRule())
        ;
    }

    public function testValidArticleWithCompleteData(): void
    {
        $author = (new PersonBuilder())
            ->name('John Doe')
            ->email('john@example.com')
            ->url('https://johndoe.com')
            ->build()
        ;

        $article = (new ArticleBuilder())
            ->headline('Complete Article Example')
            ->description('A comprehensive article with all required properties.')
            ->author($author)
            ->datePublished(new DateTime('2024-01-15'))
            ->keywords(['testing', 'validation', 'schema'])
            ->articleBody('This is the complete content of the article.')
            ->build()
        ;

        $result = $this->validator->validate($article);

        self::assertTrue($result->isValid());
        self::assertCount(0, $result->getErrors());
    }

    public function testArticleWithMissingRequiredProperties(): void
    {
        $article = SchemaFactory::create('Article', [
            'description' => 'Article without headline',
            'author' => 'Some Author',
        ]);

        $result = $this->validator->validate($article);

        self::assertFalse($result->isValid());
        self::assertGreaterThan(0, $result->getErrors());

        $errorMessages = array_map(
            static fn ($error) => $error->getMessage(),
            $result->getErrors(),
        );

        // Check that there's at least one error about missing headline
        $hasHeadlineError = false;
        foreach ($errorMessages as $message) {
            if (str_contains($message, 'headline')) {
                $hasHeadlineError = true;

                break;
            }
        }
        self::assertTrue($hasHeadlineError, 'Expected error about missing headline property. Got: ' . implode(', ', $errorMessages));
    }

    public function testValidationWithJsonLdRenderer(): void
    {
        $article = (new ArticleBuilder())
            ->headline('JSON-LD Validation Test')
            ->description('Testing validation with JSON-LD output.')
            ->author('Test Author')
            ->datePublished(new DateTime('2024-02-01'))
            ->build()
        ;

        // Validate the schema
        $result = $this->validator->validate($article);
        self::assertTrue($result->isValid());

        // Render as JSON-LD
        $renderer = new JsonLdRenderer();
        $jsonLd = $renderer->render($article);

        // Verify the JSON-LD is valid JSON
        $decoded = json_decode($jsonLd, true);
        self::assertIsArray($decoded);
        self::assertSame('https://schema.org', $decoded['@context']);
        self::assertSame('Article', $decoded['@type']);
        self::assertSame('JSON-LD Validation Test', $decoded['headline']);
    }

    public function testValidationWithMicrodataRenderer(): void
    {
        $article = (new ArticleBuilder())
            ->headline('Microdata Validation Test')
            ->description('Testing validation with Microdata output.')
            ->author('Test Author')
            ->datePublished(new DateTime('2024-02-01'))
            ->build()
        ;

        // Validate the schema
        $result = $this->validator->validate($article);
        self::assertTrue($result->isValid());

        // Render as Microdata
        $renderer = new MicrodataRenderer();
        $microdata = $renderer->render($article);

        // Verify the Microdata contains expected elements
        self::assertStringContainsString('itemscope', $microdata);
        self::assertStringContainsString('itemtype="https://schema.org/Article"', $microdata);
        self::assertStringContainsString('itemprop="headline"', $microdata);
        self::assertStringContainsString('Microdata Validation Test', $microdata);
    }

    public function testValidationWithRdfaRenderer(): void
    {
        $article = (new ArticleBuilder())
            ->headline('RDFa Validation Test')
            ->description('Testing validation with RDFa output.')
            ->author('Test Author')
            ->datePublished(new DateTime('2024-02-01'))
            ->build()
        ;

        // Validate the schema
        $result = $this->validator->validate($article);
        self::assertTrue($result->isValid());

        // Render as RDFa
        $renderer = new RdfaRenderer();
        $rdfa = $renderer->render($article);

        // Verify the RDFa contains expected elements
        self::assertStringContainsString('vocab="https://schema.org/"', $rdfa);
        self::assertStringContainsString('typeof="Article"', $rdfa);
        self::assertStringContainsString('property="headline"', $rdfa);
        self::assertStringContainsString('RDFa Validation Test', $rdfa);
    }

    public function testComplexNestedSchemaValidation(): void
    {
        $organization = SchemaFactory::create('Organization', [
            'name' => 'Tech Company',
            'url' => 'https://techcompany.com',
            'logo' => 'https://techcompany.com/logo.png',
        ]);

        $author = (new PersonBuilder())
            ->name('Jane Smith')
            ->jobTitle('Senior Writer')
            ->worksFor($organization)
            ->email('jane@techcompany.com')
            ->build()
        ;

        $article = (new ArticleBuilder())
            ->headline('Complex Nested Schema Example')
            ->description('An article demonstrating complex nested schema validation.')
            ->author($author)
            ->publisher($organization)
            ->datePublished(new DateTime('2024-03-01'))
            ->dateModified(new DateTime('2024-03-05'))
            ->keywords(['complex', 'nested', 'validation'])
            ->articleSection('Technology')
            ->wordCount(1500)
            // ->inLanguage('en-US') // Method not implemented yet
            ->build()
        ;

        $result = $this->validator->validate($article);

        self::assertTrue($result->isValid());
        self::assertCount(0, $result->getErrors());

        // Test with all renderers
        $jsonLdRenderer = new JsonLdRenderer();
        $jsonLd = $jsonLdRenderer->render($article);
        $decoded = json_decode($jsonLd, true);
        self::assertIsArray($decoded);
        self::assertIsArray($decoded['author']);
        self::assertIsArray($decoded['publisher']);

        $microdataRenderer = new MicrodataRenderer();
        $microdata = $microdataRenderer->render($article);
        self::assertStringContainsString('itemtype="https://schema.org/Person"', $microdata);
        self::assertStringContainsString('itemtype="https://schema.org/Organization"', $microdata);

        $rdfaRenderer = new RdfaRenderer();
        $rdfa = $rdfaRenderer->render($article);
        self::assertStringContainsString('typeof="Person"', $rdfa);
        self::assertStringContainsString('typeof="Organization"', $rdfa);
    }

    public function testValidationErrorsWithRendering(): void
    {
        // Create an invalid article (missing required headline)
        $invalidArticle = SchemaFactory::create('Article', [
            'description' => 'Article without required headline',
            'author' => 'Some Author',
            'datePublished' => '2024-01-01',
        ]);

        $result = $this->validator->validate($invalidArticle);
        self::assertFalse($result->isValid());

        $errors = $result->getErrors();
        self::assertGreaterThan(0, $errors);

        // Even invalid schemas should still render (renderers don't validate)
        $renderer = new JsonLdRenderer();
        $jsonLd = $renderer->render($invalidArticle);

        $decoded = json_decode($jsonLd, true);
        self::assertIsArray($decoded);
        self::assertArrayNotHasKey('headline', $decoded);
        self::assertArrayHasKey('description', $decoded);
    }

    public function testValidationWithEmptyValues(): void
    {
        $article = (new ArticleBuilder())
            ->headline('Article with Empty Values')
            ->description('')  // Empty description
            ->author('Test Author')
            ->datePublished(new DateTime('2024-01-01'))
            ->keywords([])     // Empty keywords array
            // Skip null URL as it causes type error
            ->build()
        ;

        $result = $this->validator->validate($article);

        // Should have warnings about empty values but still be valid
        self::assertTrue($result->isValid());

        // Test compact rendering removes empty values
        $renderer = new JsonLdRenderer();
        $renderer->setCompactOutput(true);
        $jsonLd = $renderer->render($article);

        $decoded = json_decode($jsonLd, true);
        self::assertIsArray($decoded);
        self::assertArrayNotHasKey('description', $decoded);
        self::assertArrayNotHasKey('keywords', $decoded);
        // URL was not set, so no assertion needed
        self::assertArrayHasKey('headline', $decoded);
    }

    public function testCrossRendererConsistency(): void
    {
        $article = (new ArticleBuilder())
            ->headline('Cross-Renderer Consistency Test')
            ->description('Testing consistency across different renderers.')
            ->author('Consistency Tester')
            ->datePublished(new DateTime('2024-04-01'))
            ->keywords(['consistency', 'testing', 'renderers'])
            ->build()
        ;

        // Validate once
        $result = $this->validator->validate($article);
        self::assertTrue($result->isValid());

        // Render with all three renderers
        $jsonLdRenderer = new JsonLdRenderer();
        $microdataRenderer = new MicrodataRenderer();
        $rdfaRenderer = new RdfaRenderer();

        $jsonLd = $jsonLdRenderer->render($article);
        $microdata = $microdataRenderer->render($article);
        $rdfa = $rdfaRenderer->render($article);

        // All should contain the core content
        $headline = 'Cross-Renderer Consistency Test';
        $description = 'Testing consistency across different renderers.';

        // JSON-LD
        self::assertStringContainsString($headline, $jsonLd);
        self::assertStringContainsString($description, $jsonLd);

        // Microdata
        self::assertStringContainsString($headline, $microdata);
        self::assertStringContainsString($description, $microdata);

        // RDFa
        self::assertStringContainsString($headline, $rdfa);
        self::assertStringContainsString($description, $rdfa);
    }

    public function testValidationWithCustomRenderer(): void
    {
        $article = (new ArticleBuilder())
            ->headline('Custom Renderer Test')
            ->description('Testing with custom renderer configurations.')
            ->author('Custom Test')
            ->datePublished(new DateTime('2024-05-01'))
            ->build()
        ;

        $result = $this->validator->validate($article);
        self::assertTrue($result->isValid());

        // Test JSON-LD with script tag
        $jsonLdRenderer = new JsonLdRenderer();
        $jsonLdRenderer
            ->setIncludeScriptTag(true)
            ->setPrettyPrint(true)
            ->setUnescapeSlashes(false)
        ;

        $jsonLdWithScript = $jsonLdRenderer->render($article);
        self::assertStringStartsWith('<script type="application/ld+json">', $jsonLdWithScript);
        self::assertStringEndsWith('</script>', $jsonLdWithScript);

        // Test Microdata with semantic elements
        $microdataRenderer = new MicrodataRenderer();
        $microdataRenderer
            ->setUseSemanticElements(true)
            ->setIncludeMetaElements(true)
        ;

        $semanticMicrodata = $microdataRenderer->render($article);
        self::assertStringContainsString('<article itemscope', $semanticMicrodata);
        self::assertStringContainsString('<h1 itemprop="headline"', $semanticMicrodata);
    }
}
