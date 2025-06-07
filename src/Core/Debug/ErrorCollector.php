<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Debug;

use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Core\Exceptions\InvalidPropertyException;
use Inesta\Schemas\Core\Exceptions\SchemaException;
use Inesta\Schemas\Core\Exceptions\ValidationException;
use Inesta\Schemas\Validation\ValidationError;
use Throwable;

use function array_map;
use function array_slice;
use function count;
use function date;
use function get_class;
use function implode;
use function is_array;
use function is_string;
use function json_encode;
use function mb_strlen;
use function mb_strrpos;
use function mb_substr;
use function sprintf;
use function str_contains;

/**
 * Collects and formats errors with helpful debugging information.
 *
 * Provides detailed error messages with suggestions for common issues
 * and contextual information to help developers resolve problems.
 */
final class ErrorCollector
{
    /** @var array<array<string, mixed>> */
    private array $errors = [];

    /** @var array<string, string> */
    private array $suggestions = [
        'InvalidPropertyException' => 'Check that the property name is valid for this schema type. Refer to Schema.org documentation.',
        'ValidationException' => 'Ensure all required properties are set and values match expected types.',
        'SchemaException' => 'Verify that the schema type exists and is properly configured.',
    ];

    /**
     * Collect an error with context information.
     *
     * @param Throwable                $error   The error that occurred
     * @param SchemaTypeInterface|null $schema  The schema context (if available)
     * @param array<string, mixed>     $context Additional context information
     */
    public function collectError(
        Throwable $error,
        ?SchemaTypeInterface $schema = null,
        array $context = [],
    ): void {
        $this->errors[] = [
            'type' => get_class($error),
            'message' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'schema_type' => $schema?->getType(),
            'schema_context' => $schema?->getContext(),
            'context' => $context,
            'suggestion' => $this->getSuggestion($error),
            'timestamp' => date('c'),
            'trace' => $this->formatTrace($error->getTrace()),
        ];
    }

    /**
     * Collect validation errors.
     *
     * @param array<ValidationError>   $validationErrors The validation errors
     * @param SchemaTypeInterface|null $schema           The schema context
     */
    public function collectValidationErrors(
        array $validationErrors,
        ?SchemaTypeInterface $schema = null,
    ): void {
        foreach ($validationErrors as $error) {
            $this->errors[] = [
                'type' => 'ValidationError',
                'message' => $error->getMessage(),
                'property' => $error->getProperty(),
                'value' => $error->getValue(),
                'schema_type' => $schema?->getType(),
                'schema_context' => $schema?->getContext(),
                'suggestion' => $this->getValidationSuggestion($error),
                'timestamp' => date('c'),
            ];
        }
    }

    /**
     * Get all collected errors.
     *
     * @return array<array<string, mixed>> All collected errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Clear all collected errors.
     */
    public function clear(): void
    {
        $this->errors = [];
    }

    /**
     * Check if any errors have been collected.
     *
     * @return bool True if errors exist
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get the count of collected errors.
     *
     * @return int Number of errors
     */
    public function getErrorCount(): int
    {
        return count($this->errors);
    }

    /**
     * Format all errors as a readable report.
     *
     * @return string Formatted error report
     */
    public function formatErrors(): string
    {
        if ($this->errors === []) {
            return 'No errors collected.';
        }

        $output = [];
        $output[] = sprintf('=== Error Report (%d errors) ===', count($this->errors));
        $output[] = '';

        foreach ($this->errors as $index => $error) {
            $output[] = sprintf('Error #%d: %s', $index + 1, $error['type']);
            $output[] = sprintf('Message: %s', $error['message']);

            if (($error['schema_type'] ?? null) !== null) {
                $output[] = sprintf('Schema Type: %s', (string) $error['schema_type']);
            }

            if (($error['property'] ?? null) !== null) {
                $output[] = sprintf('Property: %s', (string) $error['property']);
            }

            if (array_key_exists('value', $error)) {
                $output[] = sprintf('Value: %s', $this->formatValue($error['value']));
            }

            if (($error['file'] ?? null) !== null) {
                $output[] = sprintf('Location: %s:%d', (string) $error['file'], (int) ($error['line'] ?? 0));
            }

            if (($error['suggestion'] ?? null) !== null) {
                $output[] = sprintf('Suggestion: %s', (string) $error['suggestion']);
            }

            if (($error['context'] ?? []) !== []) {
                $output[] = 'Context:';
                if (is_array($error['context'])) {
                    foreach ($error['context'] as $key => $value) {
                        $output[] = sprintf('  %s: %s', (string) $key, $this->formatValue($value));
                    }
                }
            }

            $output[] = sprintf('Timestamp: %s', $error['timestamp']);
            $output[] = '';
        }

        return implode("\n", $output);
    }

    /**
     * Get errors grouped by type.
     *
     * @return array<string, array<array<string, mixed>>> Errors grouped by type
     */
    public function getErrorsByType(): array
    {
        $grouped = [];

        foreach ($this->errors as $error) {
            $type = $error['type'];
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $error;
        }

        return $grouped;
    }

    /**
     * Get summary of error types and counts.
     *
     * @return array<string, int> Error type counts
     */
    public function getErrorSummary(): array
    {
        $summary = [];

        foreach ($this->errors as $error) {
            $type = (string) ($error['type'] ?? 'unknown');
            $count = $summary[$type] ?? 0;
            $summary[$type] = $count + 1;
        }

        return $summary;
    }

    /**
     * Export errors as JSON.
     *
     * @return string JSON representation of errors
     */
    public function exportAsJson(): string
    {
        $result = json_encode($this->errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        return $result !== false ? $result : '{}';
    }

    /**
     * Get a helpful suggestion for the given error.
     *
     * @param Throwable $error The error to suggest for
     *
     * @return string Helpful suggestion
     */
    private function getSuggestion(Throwable $error): string
    {
        $className = get_class($error);
        $baseClass = mb_substr($className, mb_strrpos($className, '\\') + 1);

        // Check for specific error patterns
        if ($error instanceof InvalidPropertyException) {
            return $this->getPropertySuggestion($error);
        }

        if ($error instanceof ValidationException) {
            return $this->getValidationExceptionSuggestion($error);
        }

        if ($error instanceof SchemaException) {
            return $this->getSchemaSuggestion($error);
        }

        return $this->suggestions[$baseClass] ?? 'Review the error message and Schema.org documentation for guidance.';
    }

    /**
     * Get suggestion for property-related errors.
     *
     * @param InvalidPropertyException $error The property error
     *
     * @return string Property-specific suggestion
     */
    private function getPropertySuggestion(InvalidPropertyException $error): string
    {
        $message = $error->getMessage();

        if (str_contains($message, 'not allowed')) {
            return 'This property is not valid for this schema type. Check Schema.org documentation for allowed properties.';
        }

        if (str_contains($message, 'required')) {
            return 'This property is required for this schema type. Ensure it has a valid value.';
        }

        if (str_contains($message, 'type')) {
            return 'The property value type is incorrect. Check the expected type in Schema.org documentation.';
        }

        return 'Verify the property name and value against Schema.org specifications.';
    }

    /**
     * Get suggestion for validation errors.
     *
     * @param ValidationError $error The validation error
     *
     * @return string Validation-specific suggestion
     */
    private function getValidationSuggestion(ValidationError $error): string
    {
        $message = $error->getMessage();

        if (str_contains($message, 'required')) {
            return sprintf('The property "%s" is required. Add this property with a valid value.', $error->getProperty());
        }

        if (str_contains($message, 'empty')) {
            return sprintf('The property "%s" cannot be empty. Provide a valid value.', $error->getProperty());
        }

        if (str_contains($message, 'type')) {
            return sprintf('The property "%s" has an invalid type. Check Schema.org for the expected type.', $error->getProperty());
        }

        return sprintf('Review the value for property "%s" and ensure it meets Schema.org requirements.', $error->getProperty());
    }

    /**
     * Get suggestion for validation exceptions.
     *
     * @param ValidationException $error The validation exception
     *
     * @return string Validation exception suggestion
     */
    private function getValidationExceptionSuggestion(ValidationException $error): string
    {
        $message = $error->getMessage();

        if (str_contains($message, 'failed')) {
            return 'Schema validation failed. Check all required properties and their values.';
        }

        return 'Ensure all schema properties are valid and meet Schema.org requirements.';
    }

    /**
     * Get suggestion for schema-related errors.
     *
     * @param SchemaException $error The schema error
     *
     * @return string Schema-specific suggestion
     */
    private function getSchemaSuggestion(SchemaException $error): string
    {
        $message = $error->getMessage();

        if (str_contains($message, 'not found')) {
            return 'The schema type does not exist. Check the type name against Schema.org documentation.';
        }

        if (str_contains($message, 'invalid')) {
            return 'The schema configuration is invalid. Review the schema setup and properties.';
        }

        return 'Check the schema type and configuration against Schema.org specifications.';
    }

    /**
     * Format a value for display in error messages.
     *
     * @param mixed $value The value to format
     *
     * @return string Formatted value
     */
    private function formatValue(mixed $value): string
    {
        if ($value instanceof SchemaTypeInterface) {
            return sprintf('[%s Schema]', $value->getType());
        }

        if (is_array($value)) {
            return sprintf('[Array with %d items]', count($value));
        }

        if (is_string($value)) {
            return mb_strlen($value) > 100 ? mb_substr($value, 0, 97) . '...' : $value;
        }

        return json_encode($value) !== false ? json_encode($value) : 'null';
    }

    /**
     * Format stack trace for better readability.
     *
     * @param array<array<string, mixed>> $trace The stack trace
     *
     * @return array<string> Formatted trace lines
     */
    private function formatTrace(array $trace): array
    {
        return array_map(
            static function (array $frame): string {
                $file = $frame['file'] ?? 'unknown';
                $line = $frame['line'] ?? 0;
                $function = $frame['function'] ?? 'unknown';
                $class = isset($frame['class']) ? $frame['class'] . '::' : '';

                return sprintf('%s%s() at %s:%d', (string) $class, (string) $function, (string) $file, (int) $line);
            },
            array_slice($trace, 0, 5), // Limit to first 5 frames
        );
    }
}
