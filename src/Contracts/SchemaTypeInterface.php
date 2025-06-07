<?php

declare(strict_types=1);

namespace Inesta\Schemas\Contracts;

use Inesta\Schemas\Validation\ValidationResult;

/**
 * Interface for all Schema.org types.
 *
 * This interface defines the contract that all Schema.org type implementations
 * must follow. It provides methods for type identification, property management,
 * validation, and output generation in multiple formats.
 *
 * @see https://schema.org/
 */
interface SchemaTypeInterface
{
    /**
     * Get the Schema.org type name for this instance.
     *
     * @return string The Schema.org type
     */
    public function getType(): string;

    /**
     * Get all properties of this schema.
     *
     * @return array<string, mixed> Array of property name => value pairs
     */
    public function getProperties(): array;

    /**
     * Get a specific property value.
     *
     * @param string $property The property name
     *
     * @return mixed The property value or null if not set
     */
    public function getProperty(string $property): mixed;

    /**
     * Check if a property is set.
     *
     * @param string $property The property name
     *
     * @return bool True if the property is set, false otherwise
     */
    public function hasProperty(string $property): bool;

    /**
     * Set a property value and return a new instance.
     *
     * @param string $property The property name
     * @param mixed  $value    The property value
     *
     * @return static A new instance with the property set
     */
    public function withProperty(string $property, mixed $value): static;

    /**
     * Get the Schema.org context URL.
     *
     * @return string The context URL (typically 'https://schema.org')
     */
    public function getContext(): string;

    /**
     * Validate the schema against Schema.org specifications.
     *
     * @return ValidationResult The validation result containing any errors or warnings
     */
    public function validate(): ValidationResult;

    /**
     * Check if the schema is valid.
     *
     * @return bool True if valid, false otherwise
     */
    public function isValid(): bool;

    /**
     * Convert the schema to an array representation.
     *
     * @return array<string, mixed> The schema as an associative array
     */
    public function toArray(): array;

    /**
     * Convert the schema to JSON-LD format.
     *
     * @return string The schema as JSON-LD
     */
    public function toJsonLd(): string;

    /**
     * Convert the schema to Microdata format.
     *
     * @return string The schema as HTML with Microdata
     */
    public function toMicrodata(): string;

    /**
     * Convert the schema to RDFa format.
     *
     * @return string The schema as HTML with RDFa
     */
    public function toRdfa(): string;

    /**
     * Get the Schema.org type name.
     *
     * @return string The Schema.org type (e.g., 'Article', 'Person', 'Organization')
     */
    public static function getSchemaType(): string;
}
