<?php

declare(strict_types=1);

namespace Inesta\Schemas\Renderer\JsonLd;

use Inesta\Schemas\Contracts\RendererInterface;
use Inesta\Schemas\Contracts\SchemaTypeInterface;

use function is_array;
use function json_encode;

/**
 * Enhanced JSON-LD renderer with configuration options.
 *
 * Renders schemas as JSON-LD format with customizable output options
 * including compression, escaping, and formatting preferences.
 *
 * @see https://json-ld.org/
 */
final class JsonLdRenderer implements RendererInterface
{
    private bool $prettyPrint = true;

    private bool $unescapeSlashes = true;

    private bool $unescapeUnicode = true;

    private bool $includeScriptTag = false;

    private bool $compactOutput = false;

    /**
     * Set whether to pretty print the output.
     *
     * @param bool $prettyPrint Whether to pretty print
     *
     * @return static The renderer for method chaining
     */
    public function setPrettyPrint(bool $prettyPrint): static
    {
        $this->prettyPrint = $prettyPrint;

        return $this;
    }

    /**
     * Set whether to unescape slashes.
     *
     * @param bool $unescapeSlashes Whether to unescape slashes
     *
     * @return static The renderer for method chaining
     */
    public function setUnescapeSlashes(bool $unescapeSlashes): static
    {
        $this->unescapeSlashes = $unescapeSlashes;

        return $this;
    }

    /**
     * Set whether to unescape Unicode characters.
     *
     * @param bool $unescapeUnicode Whether to unescape Unicode
     *
     * @return static The renderer for method chaining
     */
    public function setUnescapeUnicode(bool $unescapeUnicode): static
    {
        $this->unescapeUnicode = $unescapeUnicode;

        return $this;
    }

    /**
     * Set whether to include HTML script tag wrapper.
     *
     * @param bool $includeScriptTag Whether to include script tag
     *
     * @return static The renderer for method chaining
     */
    public function setIncludeScriptTag(bool $includeScriptTag): static
    {
        $this->includeScriptTag = $includeScriptTag;

        return $this;
    }

    /**
     * Set whether to output compact format (removes empty properties).
     *
     * @param bool $compactOutput Whether to use compact output
     *
     * @return static The renderer for method chaining
     */
    public function setCompactOutput(bool $compactOutput): static
    {
        $this->compactOutput = $compactOutput;

        return $this;
    }

    public function render(SchemaTypeInterface $schema): string
    {
        $data = $schema->toArray();

        if ($this->compactOutput) {
            $data = $this->removeEmptyProperties($data);
        }

        $flags = JSON_THROW_ON_ERROR;

        if ($this->unescapeSlashes) {
            $flags |= JSON_UNESCAPED_SLASHES;
        }

        if ($this->unescapeUnicode) {
            $flags |= JSON_UNESCAPED_UNICODE;
        }

        if ($this->prettyPrint) {
            $flags |= JSON_PRETTY_PRINT;
        }

        $json = json_encode($data, $flags);

        if ($this->includeScriptTag) {
            return "<script type=\"application/ld+json\">\n{$json}\n</script>";
        }

        return $json;
    }

    public function getMimeType(): string
    {
        return $this->includeScriptTag ? 'text/html' : 'application/ld+json';
    }

    public function getFormat(): string
    {
        return 'json-ld';
    }

    /**
     * Render with HTML script tag wrapper.
     *
     * @param SchemaTypeInterface $schema The schema to render
     *
     * @return string The JSON-LD wrapped in HTML script tag
     */
    public function renderWithScriptTag(SchemaTypeInterface $schema): string
    {
        $originalSetting = $this->includeScriptTag;
        $this->setIncludeScriptTag(true);
        $result = $this->render($schema);
        $this->setIncludeScriptTag($originalSetting);

        return $result;
    }

    /**
     * Remove empty properties from the data array.
     *
     * @param array<string, mixed> $data The data array
     *
     * @return array<string, mixed> The cleaned data array
     */
    private function removeEmptyProperties(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($value === null || $value === '' || $value === []) {
                unset($data[$key]);
            } elseif (is_array($value)) {
                $cleaned = $this->removeEmptyProperties($value);
                if ($cleaned === []) {
                    unset($data[$key]);
                } else {
                    $data[$key] = $cleaned;
                }
            }
        }

        return $data;
    }
}
