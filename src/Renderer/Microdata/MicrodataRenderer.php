<?php

declare(strict_types=1);

namespace Inesta\Schemas\Renderer\Microdata;

use Inesta\Schemas\Contracts\RendererInterface;
use Inesta\Schemas\Contracts\SchemaTypeInterface;

use function array_map;
use function explode;
use function htmlspecialchars;
use function implode;
use function is_array;
use function is_object;
use function is_scalar;
use function json_encode;
use function method_exists;
use function str_repeat;

/**
 * Renders schemas as HTML with Microdata markup.
 *
 * @see https://html.spec.whatwg.org/multipage/microdata.html
 */
final class MicrodataRenderer implements RendererInterface
{
    public function render(SchemaTypeInterface $schema): string
    {
        $type = $schema->getType();
        $properties = $schema->getProperties();

        $html = "<div itemscope itemtype=\"{$schema->getContext()}/{$type}\">\n";

        foreach ($properties as $property => $value) {
            $html .= $this->renderProperty($property, $value);
        }

        $html .= '</div>';

        return $html;
    }

    public function getMimeType(): string
    {
        return 'text/html';
    }

    public function getFormat(): string
    {
        return 'microdata';
    }

    /**
     * Render a single property as Microdata.
     *
     * @param string $property The property name
     * @param mixed  $value    The property value
     *
     * @return string The rendered HTML
     */
    private function renderProperty(string $property, mixed $value): string
    {
        if ($value instanceof SchemaTypeInterface) {
            return "  <div itemprop=\"{$property}\">\n"
                   . $this->indentHtml($value->toMicrodata(), 2) . "\n"
                   . "  </div>\n";
        }

        if (is_array($value)) {
            $html = '';
            foreach ($value as $item) {
                $html .= $this->renderProperty($property, $item);
            }

            return $html;
        }

        $stringValue = is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))
            ? (string) $value
            : json_encode($value, JSON_THROW_ON_ERROR);
        $escapedValue = htmlspecialchars($stringValue, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return "  <span itemprop=\"{$property}\">{$escapedValue}</span>\n";
    }

    /**
     * Indent HTML content.
     *
     * @param string $html   The HTML to indent
     * @param int    $spaces The number of spaces to indent
     *
     * @return string The indented HTML
     */
    private function indentHtml(string $html, int $spaces): string
    {
        $indent = str_repeat(' ', $spaces);
        $lines = explode("\n", $html);

        return implode("\n", array_map(
            static fn (string $line): string => $line === '' ? $line : $indent . $line,
            $lines,
        ));
    }
}
