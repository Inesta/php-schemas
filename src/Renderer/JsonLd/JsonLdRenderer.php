<?php

declare(strict_types=1);

namespace Inesta\Schemas\Renderer\JsonLd;

use Inesta\Schemas\Contracts\RendererInterface;
use Inesta\Schemas\Contracts\SchemaTypeInterface;

use function json_encode;

/**
 * Renders schemas as JSON-LD format.
 *
 * @see https://json-ld.org/
 */
final class JsonLdRenderer implements RendererInterface
{
    public function render(SchemaTypeInterface $schema): string
    {
        $data = $schema->toArray();

        return json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function getMimeType(): string
    {
        return 'application/ld+json';
    }

    public function getFormat(): string
    {
        return 'json-ld';
    }
}
