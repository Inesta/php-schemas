<?php

declare(strict_types=1);

namespace Inesta\Schemas\Contracts;

/**
 * Interface for schema renderers.
 *
 * Renderers are responsible for converting schema objects into
 * specific output formats (JSON-LD, Microdata, RDFa, etc.).
 */
interface RendererInterface
{
    /**
     * Render a schema to the target format.
     *
     * @param SchemaTypeInterface $schema The schema to render
     *
     * @return string The rendered output
     */
    public function render(SchemaTypeInterface $schema): string;

    /**
     * Get the MIME type for this renderer's output.
     *
     * @return string The MIME type (e.g., 'application/ld+json', 'text/html')
     */
    public function getMimeType(): string;

    /**
     * Get the format identifier for this renderer.
     *
     * @return string The format identifier (e.g., 'json-ld', 'microdata', 'rdfa')
     */
    public function getFormat(): string;
}
