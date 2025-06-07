<?php

declare(strict_types=1);

namespace Inesta\Schemas\Adapters\Symfony\Twig;

use Inesta\Schemas\Adapters\Symfony\SchemaManager;
use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig extension for Schema.org operations.
 *
 * Provides Twig functions and filters for creating and rendering
 * Schema.org markup within Symfony templates.
 */
final class SchemaExtension extends AbstractExtension
{
    public function __construct(
        private readonly SchemaManager $schemaManager,
    ) {}

    /**
     * Get Twig functions.
     *
     * @return array<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('schema', [$this, 'createSchema']),
            new TwigFunction('schema_article', [$this, 'createArticle']),
            new TwigFunction('schema_person', [$this, 'createPerson']),
            new TwigFunction('schema_organization', [$this, 'createOrganization']),
            new TwigFunction('schema_thing', [$this, 'createThing']),
            new TwigFunction('schema_validate', [$this, 'validateSchema']),
        ];
    }

    /**
     * Get Twig filters.
     *
     * @return array<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('json_ld', [$this, 'renderJsonLd'], ['is_safe' => ['html']]),
            new TwigFilter('microdata', [$this, 'renderMicrodata'], ['is_safe' => ['html']]),
            new TwigFilter('rdfa', [$this, 'renderRdfa'], ['is_safe' => ['html']]),
            new TwigFilter('schema_render', [$this, 'renderSchema'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Create a schema of the specified type.
     *
     * @param string               $type       The schema type
     * @param array<string, mixed> $properties The schema properties
     * @param string               $context    The schema context
     *
     * @return SchemaTypeInterface The created schema
     */
    public function createSchema(string $type, array $properties = [], string $context = 'https://schema.org'): SchemaTypeInterface
    {
        return $this->schemaManager->create($type, $properties, $context);
    }

    /**
     * Create an Article schema.
     *
     * @param array<string, mixed> $properties The article properties
     *
     * @return SchemaTypeInterface The created Article
     */
    public function createArticle(array $properties = []): SchemaTypeInterface
    {
        return $this->schemaManager->article($properties);
    }

    /**
     * Create a Person schema.
     *
     * @param array<string, mixed> $properties The person properties
     *
     * @return SchemaTypeInterface The created Person
     */
    public function createPerson(array $properties = []): SchemaTypeInterface
    {
        return $this->schemaManager->person($properties);
    }

    /**
     * Create an Organization schema.
     *
     * @param array<string, mixed> $properties The organization properties
     *
     * @return SchemaTypeInterface The created Organization
     */
    public function createOrganization(array $properties = []): SchemaTypeInterface
    {
        return $this->schemaManager->organization($properties);
    }

    /**
     * Create a Thing schema.
     *
     * @param array<string, mixed> $properties The thing properties
     *
     * @return SchemaTypeInterface The created Thing
     */
    public function createThing(array $properties = []): SchemaTypeInterface
    {
        return $this->schemaManager->thing($properties);
    }

    /**
     * Validate a schema.
     *
     * @param SchemaTypeInterface $schema The schema to validate
     *
     * @return bool True if valid, false otherwise
     */
    public function validateSchema(SchemaTypeInterface $schema): bool
    {
        return $this->schemaManager->validate($schema)->isValid();
    }

    /**
     * Render schema as JSON-LD.
     *
     * @param SchemaTypeInterface $schema The schema to render
     *
     * @return string The JSON-LD output
     */
    public function renderJsonLd(SchemaTypeInterface $schema): string
    {
        return $this->schemaManager->renderJsonLd($schema);
    }

    /**
     * Render schema as Microdata.
     *
     * @param SchemaTypeInterface $schema The schema to render
     *
     * @return string The Microdata output
     */
    public function renderMicrodata(SchemaTypeInterface $schema): string
    {
        return $this->schemaManager->renderMicrodata($schema);
    }

    /**
     * Render schema as RDFa.
     *
     * @param SchemaTypeInterface $schema The schema to render
     *
     * @return string The RDFa output
     */
    public function renderRdfa(SchemaTypeInterface $schema): string
    {
        return $this->schemaManager->renderRdfa($schema);
    }

    /**
     * Render schema using the default renderer.
     *
     * @param SchemaTypeInterface $schema The schema to render
     *
     * @return string The rendered output
     */
    public function renderSchema(SchemaTypeInterface $schema): string
    {
        return $this->schemaManager->render($schema);
    }
}
