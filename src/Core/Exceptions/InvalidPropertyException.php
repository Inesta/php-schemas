<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Exceptions;

use Exception;

use function count;
use function get_class;
use function get_debug_type;
use function is_array;
use function is_object;
use function is_string;
use function json_encode;
use function mb_strlen;
use function mb_substr;
use function sprintf;

/**
 * Exception thrown when an invalid property is accessed or set.
 */
final class InvalidPropertyException extends SchemaException
{
    public function __construct(
        private readonly string $property,
        private readonly string $schemaType,
        string $reason = '',
        int $code = 0,
        ?Exception $previous = null,
        private readonly mixed $value = null,
    ) {
        $message = "Invalid property '{$property}' for schema type '{$schemaType}'";

        if ($reason !== '') {
            $message .= ": {$reason}";
        }

        // Add helpful context and suggestions
        $message .= "\n\nSuggestion: Check Schema.org documentation for valid properties for the {$schemaType} type.";
        $message .= "\nReference: https://schema.org/{$schemaType}";

        if ($this->value !== null) {
            $message .= sprintf("\nAttempted value: %s", $this->formatValue($this->value));
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the property name that caused the exception.
     *
     * @return string The property name
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * Get the schema type that was being accessed.
     *
     * @return string The schema type
     */
    public function getSchemaType(): string
    {
        return $this->schemaType;
    }

    /**
     * Get the value that was attempted to be set.
     *
     * @return mixed The attempted value
     */
    public function getValue(): mixed
    {
        return $this->value;
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
        if (is_string($value)) {
            return mb_strlen($value) > 50 ? '"' . mb_substr($value, 0, 47) . '..."' : '"' . $value . '"';
        }

        if (is_array($value)) {
            return sprintf('[Array with %d items]', count($value));
        }

        if (is_object($value)) {
            return sprintf('[%s object]', get_class($value));
        }

        return json_encode($value) ?: 'null';
    }

    /**
     * Create an exception for an unknown property.
     *
     * @param string $property   The unknown property name
     * @param string $schemaType The schema type
     * @param mixed  $value      The attempted value
     *
     * @return self The exception instance
     */
    public static function unknownProperty(string $property, string $schemaType, mixed $value = null): self
    {
        return new self(
            $property,
            $schemaType,
            'property is not defined in Schema.org specification',
            0,
            null,
            $value,
        );
    }

    /**
     * Create an exception for an invalid property value.
     *
     * @param string $property   The property name
     * @param string $schemaType The schema type
     * @param mixed  $value      The invalid value
     * @param string $expected   The expected type/format
     *
     * @return self The exception instance
     */
    public static function invalidValue(string $property, string $schemaType, mixed $value, string $expected): self
    {
        $reason = sprintf('invalid value type. Expected: %s, got: %s', $expected, get_debug_type($value));

        return new self(
            $property,
            $schemaType,
            $reason,
            0,
            null,
            $value,
        );
    }

    /**
     * Create an exception for a required property.
     *
     * @param string $property   The required property name
     * @param string $schemaType The schema type
     *
     * @return self The exception instance
     */
    public static function requiredProperty(string $property, string $schemaType): self
    {
        return new self(
            $property,
            $schemaType,
            'this property is required but was not provided',
        );
    }
}
