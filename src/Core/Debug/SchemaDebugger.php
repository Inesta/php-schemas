<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Debug;

use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer;
use Inesta\Schemas\Renderer\Microdata\MicrodataRenderer;
use Inesta\Schemas\Renderer\Rdfa\RdfaRenderer;
use Inesta\Schemas\Validation\ValidationEngine;

use function array_map;
use function array_slice;
use function array_unique;
use function count;
use function date;
use function get_class;
use function get_debug_type;
use function implode;
use function is_array;
use function is_string;
use function json_encode;
use function max;
use function mb_strlen;
use function mb_strtoupper;
use function mb_substr;
use function memory_get_peak_usage;
use function memory_get_usage;
use function microtime;
use function round;
use function sprintf;
use function str_contains;

/**
 * Debug utility for Schema.org types and validation.
 *
 * Provides detailed information about schema structure, properties,
 * validation status, and rendering performance.
 */
final class SchemaDebugger
{
    public function __construct(
        private readonly ValidationEngine $validator = new ValidationEngine(),
    ) {}

    /**
     * Get comprehensive debug information for a schema.
     *
     * @param SchemaTypeInterface $schema The schema to debug
     *
     * @return array<string, mixed> Debug information
     */
    public function getDebugInfo(SchemaTypeInterface $schema): array
    {
        return [
            'type' => $schema->getType(),
            'context' => $schema->getContext(),
            'class' => get_class($schema),
            'properties' => $this->getPropertiesInfo($schema),
            'validation' => $this->getValidationInfo($schema),
            'structure' => $this->getStructureInfo($schema),
            'metadata' => $this->getMetadata($schema),
        ];
    }

    /**
     * Format debug information as readable text.
     *
     * @param SchemaTypeInterface $schema The schema to debug
     *
     * @return string Formatted debug output
     */
    public function formatDebugInfo(SchemaTypeInterface $schema): string
    {
        $info = $this->getDebugInfo($schema);
        $output = [];

        $output[] = '=== Schema Debug Information ===';
        $output[] = sprintf('Type: %s', (string) $info['type']);
        $output[] = sprintf('Context: %s', (string) $info['context']);
        $output[] = sprintf('Class: %s', (string) $info['class']);
        $output[] = sprintf('Generated: %s', date('Y-m-d H:i:s'));
        $output[] = '';

        // Properties section
        $output[] = '--- Properties ---';
        if (is_array($info['properties'])) {
            foreach ($info['properties'] as $property => $details) {
                if (is_array($details)) {
                    $output[] = sprintf('  %s: %s', (string) $property, $this->formatPropertyValue($details['value'] ?? null));
                    if (($details['type'] ?? null) !== null) {
                        $output[] = sprintf('    Type: %s', (string) $details['type']);
                    }
                    if (($details['errors'] ?? []) !== []) {
                        $output[] = sprintf('    Errors: %s', implode(', ', (array) $details['errors']));
                    }
                }
            }
        }
        $output[] = '';

        // Validation section
        $validation = $info['validation'] ?? [];
        $output[] = '--- Validation ---';
        if (is_array($validation)) {
            $output[] = sprintf('  Valid: %s', ($validation['valid'] ?? false) ? 'Yes' : 'No');
            $output[] = sprintf('  Error Count: %d', (int) ($validation['error_count'] ?? 0));
            if (($validation['errors'] ?? []) !== []) {
                $output[] = '  Errors:';
                foreach ((array) $validation['errors'] as $error) {
                    $output[] = sprintf('    - %s', (string) $error);
                }
            }
        }
        $output[] = '';

        // Structure section
        $structure = $info['structure'] ?? [];
        $output[] = '--- Structure ---';
        if (is_array($structure)) {
            $output[] = sprintf('  Property Count: %d', (int) ($structure['property_count'] ?? 0));
            $output[] = sprintf('  Nested Schemas: %d', (int) ($structure['nested_count'] ?? 0));
            $output[] = sprintf('  Max Depth: %d', (int) ($structure['max_depth'] ?? 0));
            if (($structure['nested_types'] ?? []) !== []) {
                $output[] = sprintf('  Nested Types: %s', implode(', ', (array) $structure['nested_types']));
            }
        }

        return implode("\n", $output);
    }

    /**
     * Analyze rendering performance for different formats.
     *
     * @param SchemaTypeInterface $schema The schema to analyze
     *
     * @return array<string, array<string, mixed>> Performance metrics
     */
    public function analyzeRenderingPerformance(SchemaTypeInterface $schema): array
    {
        $results = [];

        // Test JSON-LD rendering
        $start = microtime(true);
        $jsonLdRenderer = new JsonLdRenderer();
        $jsonLd = $jsonLdRenderer->render($schema);
        $jsonLdTime = microtime(true) - $start;

        $results['json-ld'] = [
            'time_ms' => round($jsonLdTime * 1000, 2),
            'size_bytes' => mb_strlen($jsonLd),
            'memory_used' => memory_get_usage(true),
        ];

        // Test Microdata rendering
        $start = microtime(true);
        $microdataRenderer = new MicrodataRenderer();
        $microdata = $microdataRenderer->render($schema);
        $microdataTime = microtime(true) - $start;

        $results['microdata'] = [
            'time_ms' => round($microdataTime * 1000, 2),
            'size_bytes' => mb_strlen($microdata),
            'memory_used' => memory_get_usage(true),
        ];

        // Test RDFa rendering
        $start = microtime(true);
        $rdfaRenderer = new RdfaRenderer();
        $rdfa = $rdfaRenderer->render($schema);
        $rdfaTime = microtime(true) - $start;

        $results['rdfa'] = [
            'time_ms' => round($rdfaTime * 1000, 2),
            'size_bytes' => mb_strlen($rdfa),
            'memory_used' => memory_get_usage(true),
        ];

        return $results;
    }

    /**
     * Generate a comprehensive debug report.
     *
     * @param SchemaTypeInterface $schema The schema to report on
     *
     * @return string Complete debug report
     */
    public function generateReport(SchemaTypeInterface $schema): string
    {
        $output = [];
        $output[] = $this->formatDebugInfo($schema);
        $output[] = '';

        // Add performance analysis
        $performance = $this->analyzeRenderingPerformance($schema);
        $output[] = '--- Rendering Performance ---';
        foreach ($performance as $format => $metrics) {
            $output[] = sprintf('  %s:', mb_strtoupper($format));
            $output[] = sprintf('    Time: %s ms', $metrics['time_ms']);
            $output[] = sprintf('    Size: %d bytes', $metrics['size_bytes']);
            $output[] = sprintf('    Memory: %d bytes', $metrics['memory_used']);
        }

        return implode("\n", $output);
    }

    /**
     * Get detailed properties information.
     *
     * @param SchemaTypeInterface $schema The schema to analyze
     *
     * @return array<string, array<string, mixed>> Properties information
     */
    private function getPropertiesInfo(SchemaTypeInterface $schema): array
    {
        $properties = [];
        $allProperties = $schema->getProperties();

        foreach ($allProperties as $property => $value) {
            $properties[$property] = [
                'value' => $value,
                'type' => $this->getValueType($value),
                'errors' => $this->getPropertyErrors($schema, $property),
            ];
        }

        return $properties;
    }

    /**
     * Get validation information.
     *
     * @param SchemaTypeInterface $schema The schema to validate
     *
     * @return array<string, mixed> Validation information
     */
    private function getValidationInfo(SchemaTypeInterface $schema): array
    {
        $result = $this->validator->validate($schema);

        return [
            'valid' => $result->isValid(),
            'error_count' => count($result->getErrors()),
            'errors' => array_map(
                static fn ($error): string => $error->getMessage(),
                $result->getErrors(),
            ),
        ];
    }

    /**
     * Get structure information about the schema.
     *
     * @param SchemaTypeInterface $schema The schema to analyze
     *
     * @return array<string, mixed> Structure information
     */
    private function getStructureInfo(SchemaTypeInterface $schema): array
    {
        $properties = $schema->getProperties();
        $nestedSchemas = [];
        $maxDepth = 1;

        foreach ($properties as $value) {
            if ($value instanceof SchemaTypeInterface) {
                $nestedSchemas[] = $value->getType();
                $depth = $this->calculateDepth($value, 2);
                $maxDepth = max($maxDepth, $depth);
            }
        }

        return [
            'property_count' => count($properties),
            'nested_count' => count($nestedSchemas),
            'nested_types' => array_unique($nestedSchemas),
            'max_depth' => $maxDepth,
        ];
    }

    /**
     * Get metadata about the schema.
     *
     * @param SchemaTypeInterface $schema The schema to analyze
     *
     * @return array<string, mixed> Metadata
     */
    private function getMetadata(SchemaTypeInterface $schema): array
    {
        return [
            'timestamp' => date('c'),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ];
    }

    /**
     * Calculate the depth of nested schemas.
     *
     * @param SchemaTypeInterface $schema       The schema to analyze
     * @param int                 $currentDepth Current depth level
     *
     * @return int Maximum depth found
     */
    private function calculateDepth(SchemaTypeInterface $schema, int $currentDepth): int
    {
        $maxDepth = $currentDepth;
        $properties = $schema->getProperties();

        foreach ($properties as $value) {
            if ($value instanceof SchemaTypeInterface) {
                $depth = $this->calculateDepth($value, $currentDepth + 1);
                $maxDepth = max($maxDepth, $depth);
            }
        }

        return $maxDepth;
    }

    /**
     * Get the type of a property value.
     *
     * @param mixed $value The value to analyze
     *
     * @return string Value type description
     */
    private function getValueType(mixed $value): string
    {
        if ($value instanceof SchemaTypeInterface) {
            return sprintf('Schema (%s)', $value->getType());
        }

        if (is_array($value)) {
            return sprintf('Array (%d items)', count($value));
        }

        return get_debug_type($value);
    }

    /**
     * Format a property value for display.
     *
     * @param mixed $value The value to format
     *
     * @return string Formatted value
     */
    private function formatPropertyValue(mixed $value): string
    {
        if ($value instanceof SchemaTypeInterface) {
            return sprintf('[%s Schema]', $value->getType());
        }

        if (is_array($value)) {
            if (empty($value)) {
                return '[]';
            }

            return sprintf('[%s]', implode(', ', array_map(
                static fn ($item): string => is_string($item) ? $item : json_encode($item),
                array_slice($value, 0, 3),
            )) . (count($value) > 3 ? '...' : ''));
        }

        if (is_string($value)) {
            return mb_strlen($value) > 50 ? mb_substr($value, 0, 47) . '...' : $value;
        }

        return json_encode($value) ?: 'null';
    }

    /**
     * Get validation errors for a specific property.
     *
     * @param SchemaTypeInterface $schema   The schema
     * @param string              $property The property name
     *
     * @return array<string> Property-specific errors
     */
    private function getPropertyErrors(SchemaTypeInterface $schema, string $property): array
    {
        $result = $this->validator->validate($schema);
        $errors = [];

        foreach ($result->getErrors() as $error) {
            if (str_contains($error->getMessage(), $property)) {
                $errors[] = $error->getMessage();
            }
        }

        return $errors;
    }
}
