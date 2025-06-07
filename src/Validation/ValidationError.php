<?php

declare(strict_types=1);

namespace Inesta\Schemas\Validation;

use JsonSerializable;

use function get_debug_type;

/**
 * Represents a validation error or warning.
 */
final readonly class ValidationError implements JsonSerializable
{
    /**
     * @param string      $message  The error message
     * @param string      $code     The error code
     * @param string|null $property The property that caused the error (if applicable)
     * @param mixed       $value    The invalid value (if applicable)
     */
    public function __construct(
        private string $message,
        private string $code,
        private ?string $property = null,
        private mixed $value = null,
    ) {}

    /**
     * Get the error message.
     *
     * @return string The error message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get the error code.
     *
     * @return string The error code
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Get the error type (alias for getCode for consistency).
     *
     * @return string The error type/code
     */
    public function getType(): string
    {
        return $this->code;
    }

    /**
     * Get the property that caused the error.
     *
     * @return string|null The property name or null if not applicable
     */
    public function getProperty(): ?string
    {
        return $this->property;
    }

    /**
     * Get the invalid value.
     *
     * @return mixed The invalid value or null if not applicable
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Convert to array representation for JSON serialization.
     *
     * @return array<string, mixed> The array representation
     */
    public function jsonSerialize(): array
    {
        return [
            'message' => $this->message,
            'code' => $this->code,
            'property' => $this->property,
            'value' => $this->value,
        ];
    }

    /**
     * Create a validation error for a required property.
     *
     * @param string $property The required property name
     *
     * @return self The validation error
     */
    public static function requiredProperty(string $property): self
    {
        return new self(
            "Required property '{$property}' is missing",
            'REQUIRED_PROPERTY_MISSING',
            $property,
        );
    }

    /**
     * Create a validation error for an invalid property type.
     *
     * @param string $property     The property name
     * @param string $expectedType The expected type
     * @param mixed  $actualValue  The actual value
     *
     * @return self The validation error
     */
    public static function invalidPropertyType(string $property, string $expectedType, mixed $actualValue): self
    {
        $actualType = get_debug_type($actualValue);

        return new self(
            "Property '{$property}' expects type '{$expectedType}', got '{$actualType}'",
            'INVALID_PROPERTY_TYPE',
            $property,
            $actualValue,
        );
    }

    /**
     * Create a validation error for an invalid property value.
     *
     * @param string $property The property name
     * @param mixed  $value    The invalid value
     * @param string $reason   The reason why the value is invalid
     *
     * @return self The validation error
     */
    public static function invalidPropertyValue(string $property, mixed $value, string $reason): self
    {
        return new self(
            "Property '{$property}' has invalid value: {$reason}",
            'INVALID_PROPERTY_VALUE',
            $property,
            $value,
        );
    }

    /**
     * Create a validation error for an unknown property.
     *
     * @param string $property The unknown property name
     *
     * @return self The validation error
     */
    public static function unknownProperty(string $property): self
    {
        return new self(
            "Unknown property '{$property}' for this schema type",
            'UNKNOWN_PROPERTY',
            $property,
        );
    }
}
