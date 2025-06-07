<?php

declare(strict_types=1);

namespace Inesta\Schemas\Adapters\Symfony;

use Inesta\Schemas\Builder\Factory\SchemaFactory;
use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer;
use Inesta\Schemas\Renderer\Microdata\MicrodataRenderer;
use Inesta\Schemas\Renderer\Rdfa\RdfaRenderer;
use Inesta\Schemas\Validation\ValidationEngine;
use Inesta\Schemas\Validation\ValidationResult;

/**
 * Symfony schema manager for simplified schema operations.
 *
 * Provides a unified interface for creating, validating, and rendering
 * Schema.org markup within Symfony applications.
 */
final class SchemaManager
{
    public function __construct(
        private readonly SchemaFactory $factory,
        private readonly JsonLdRenderer $jsonLdRenderer,
        private readonly MicrodataRenderer $microdataRenderer,
        private readonly RdfaRenderer $rdfaRenderer,
        private readonly ValidationEngine $validator,
    ) {}

    /**
     * Create a schema of the specified type.
     *
     * @param string               $type       The schema type (e.g., 'Article', 'Person')
     * @param array<string, mixed> $properties The schema properties
     * @param string               $context    The schema context URL
     *
     * @return SchemaTypeInterface The created schema instance
     */
    public function create(string $type, array $properties = [], string $context = 'https://schema.org'): SchemaTypeInterface
    {
        return $this->factory::create($type, $properties, $context);
    }

    /**
     * Create an Article schema.
     *
     * @param array<string, mixed> $properties The article properties
     *
     * @return SchemaTypeInterface The created Article instance
     */
    public function article(array $properties = []): SchemaTypeInterface
    {
        return $this->create('Article', $properties);
    }

    /**
     * Create a Person schema.
     *
     * @param array<string, mixed> $properties The person properties
     *
     * @return SchemaTypeInterface The created Person instance
     */
    public function person(array $properties = []): SchemaTypeInterface
    {
        return $this->create('Person', $properties);
    }

    /**
     * Create an Organization schema.
     *
     * @param array<string, mixed> $properties The organization properties
     *
     * @return SchemaTypeInterface The created Organization instance
     */
    public function organization(array $properties = []): SchemaTypeInterface
    {
        return $this->create('Organization', $properties);
    }

    /**
     * Create a Thing schema.
     *
     * @param array<string, mixed> $properties The thing properties
     *
     * @return SchemaTypeInterface The created Thing instance
     */
    public function thing(array $properties = []): SchemaTypeInterface
    {
        return $this->create('Thing', $properties);
    }

    /**
     * Validate a schema.
     *
     * @param SchemaTypeInterface $schema The schema to validate
     *
     * @return ValidationResult The validation result
     */
    public function validate(SchemaTypeInterface $schema): ValidationResult
    {
        return $this->validator->validate($schema);
    }

    /**
     * Render a schema using the default renderer (JSON-LD).
     *
     * @param SchemaTypeInterface $schema The schema to render
     *
     * @return string The rendered output
     */
    public function render(SchemaTypeInterface $schema): string
    {
        return $this->renderJsonLd($schema);
    }

    /**
     * Render a schema as JSON-LD.
     *
     * @param SchemaTypeInterface $schema The schema to render
     *
     * @return string The JSON-LD output
     */
    public function renderJsonLd(SchemaTypeInterface $schema): string
    {
        return $this->jsonLdRenderer->render($schema);
    }

    /**
     * Render a schema as Microdata.
     *
     * @param SchemaTypeInterface $schema The schema to render
     *
     * @return string The Microdata output
     */
    public function renderMicrodata(SchemaTypeInterface $schema): string
    {
        return $this->microdataRenderer->render($schema);
    }

    /**
     * Render a schema as RDFa.
     *
     * @param SchemaTypeInterface $schema The schema to render
     *
     * @return string The RDFa output
     */
    public function renderRdfa(SchemaTypeInterface $schema): string
    {
        return $this->rdfaRenderer->render($schema);
    }

    /**
     * Get the JSON-LD renderer instance.
     *
     * @return JsonLdRenderer The JSON-LD renderer
     */
    public function getJsonLdRenderer(): JsonLdRenderer
    {
        return $this->jsonLdRenderer;
    }

    /**
     * Get the Microdata renderer instance.
     *
     * @return MicrodataRenderer The Microdata renderer
     */
    public function getMicrodataRenderer(): MicrodataRenderer
    {
        return $this->microdataRenderer;
    }

    /**
     * Get the RDFa renderer instance.
     *
     * @return RdfaRenderer The RDFa renderer
     */
    public function getRdfaRenderer(): RdfaRenderer
    {
        return $this->rdfaRenderer;
    }

    /**
     * Get the validation engine instance.
     *
     * @return ValidationEngine The validation engine
     */
    public function getValidator(): ValidationEngine
    {
        return $this->validator;
    }
}
