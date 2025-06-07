<?php

declare(strict_types=1);

namespace Inesta\Schemas\Renderer\Rdfa;

use Inesta\Schemas\Contracts\RendererInterface;
use Inesta\Schemas\Contracts\SchemaTypeInterface;

use function array_map;
use function explode;
use function htmlspecialchars;
use function implode;
use function in_array;
use function is_array;
use function is_object;
use function is_scalar;
use function json_encode;
use function method_exists;
use function str_repeat;

/**
 * Enhanced RDFa renderer with configuration options.
 *
 * Renders schemas as HTML with RDFa markup, supporting various
 * formatting options and semantic HTML element choices.
 *
 * @see https://www.w3.org/TR/rdfa-lite/
 */
final class RdfaRenderer implements RendererInterface
{
    private bool $prettyPrint = true;

    private string $containerElement = 'div';

    private bool $useSemanticElements = false;

    private bool $includeMetaElements = true;

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
     * Set the container element for the schema.
     *
     * @param string $element The HTML element name (div, article, section, etc.)
     *
     * @return static The renderer for method chaining
     */
    public function setContainerElement(string $element): static
    {
        $this->containerElement = htmlspecialchars($element, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $this;
    }

    /**
     * Set whether to use semantic HTML elements.
     *
     * @param bool $useSemanticElements Whether to use semantic elements
     *
     * @return static The renderer for method chaining
     */
    public function setUseSemanticElements(bool $useSemanticElements): static
    {
        $this->useSemanticElements = $useSemanticElements;

        return $this;
    }

    /**
     * Set whether to include meta elements for non-visible properties.
     *
     * @param bool $includeMetaElements Whether to include meta elements
     *
     * @return static The renderer for method chaining
     */
    public function setIncludeMetaElements(bool $includeMetaElements): static
    {
        $this->includeMetaElements = $includeMetaElements;

        return $this;
    }

    public function render(SchemaTypeInterface $schema): string
    {
        $type = $schema->getType();
        $properties = $schema->getProperties();

        $element = $this->getSemanticElement($type);
        $newline = $this->prettyPrint ? "\n" : '';

        $html = "<{$element} vocab=\"{$schema->getContext()}/\" typeof=\"{$type}\">{$newline}";

        foreach ($properties as $property => $value) {
            $html .= $this->renderProperty($property, $value, 1);
        }

        $html .= "</{$element}>";

        return $html;
    }

    public function getMimeType(): string
    {
        return 'text/html';
    }

    public function getFormat(): string
    {
        return 'rdfa';
    }

    /**
     * Render a single property as RDFa.
     *
     * @param string $property    The property name
     * @param mixed  $value       The property value
     * @param int    $indentLevel The current indentation level
     *
     * @return string The rendered HTML
     */
    private function renderProperty(string $property, mixed $value, int $indentLevel = 0): string
    {
        $indent = $this->prettyPrint ? str_repeat('  ', $indentLevel) : '';
        $newline = $this->prettyPrint ? "\n" : '';

        if ($value instanceof SchemaTypeInterface) {
            return "{$indent}<div property=\"{$property}\">{$newline}"
                   . $this->indentHtml($value->toRdfa(), $indentLevel + 1) . $newline
                   . "{$indent}</div>{$newline}";
        }

        if (is_array($value)) {
            $html = '';
            foreach ($value as $item) {
                $html .= $this->renderProperty($property, $item, $indentLevel);
            }

            return $html;
        }

        $element = $this->getPropertyElement($property, $value);
        $stringValue = is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))
            ? (string) $value
            : json_encode($value, JSON_THROW_ON_ERROR);
        $escapedValue = htmlspecialchars($stringValue, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Use meta element for non-visible properties if configured
        if ($this->includeMetaElements && $this->isMetaProperty($property)) {
            return "{$indent}<meta property=\"{$property}\" content=\"{$escapedValue}\">{$newline}";
        }

        return "{$indent}<{$element} property=\"{$property}\">{$escapedValue}</{$element}>{$newline}";
    }

    /**
     * Get the appropriate semantic element for a schema type.
     *
     * @param string $type The schema type
     *
     * @return string The HTML element name
     */
    private function getSemanticElement(string $type): string
    {
        if (!$this->useSemanticElements) {
            return $this->containerElement;
        }

        return match ($type) {
            'Article' => 'article',
            'Person' => 'div',
            'Organization' => 'div',
            default => $this->containerElement,
        };
    }

    /**
     * Get the appropriate element for a property.
     *
     * @param string $property The property name
     * @param mixed  $value    The property value
     *
     * @return string The HTML element name
     */
    private function getPropertyElement(string $property, mixed $value): string
    {
        if (!$this->useSemanticElements) {
            return 'span';
        }

        return match ($property) {
            'headline', 'name' => 'h1',
            'alternativeHeadline' => 'h2',
            'description' => 'p',
            'articleBody' => 'div',
            'url' => 'a',
            'image' => 'img',
            default => 'span',
        };
    }

    /**
     * Check if a property should use meta element.
     *
     * @param string $property The property name
     *
     * @return bool True if the property should use meta element
     */
    private function isMetaProperty(string $property): bool
    {
        $metaProperties = [
            'datePublished',
            'dateModified',
            'dateCreated',
            'wordCount',
            'identifier',
        ];

        return in_array($property, $metaProperties, true);
    }

    /**
     * Indent HTML content.
     *
     * @param string $html        The HTML to indent
     * @param int    $indentLevel The indentation level (number of 2-space units)
     *
     * @return string The indented HTML
     */
    private function indentHtml(string $html, int $indentLevel): string
    {
        if (!$this->prettyPrint) {
            return $html;
        }

        $indent = str_repeat('  ', $indentLevel);
        $lines = explode("\n", $html);

        return implode("\n", array_map(
            static fn (string $line): string => $line === '' ? $line : $indent . $line,
            $lines,
        ));
    }
}
