<?php

declare(strict_types=1);

namespace Inesta\Schemas\Validation\Rules;

use DateTimeInterface;
use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Core\AbstractSchemaType;
use Inesta\Schemas\Validation\Interfaces\ValidationRuleInterface;
use Inesta\Schemas\Validation\ValidationError;
use Inesta\Schemas\Validation\ValidationResult;

use function implode;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_numeric;
use function is_object;
use function is_string;

/**
 * Validates property types based on Schema.org specifications.
 */
final class PropertyTypesRule implements ValidationRuleInterface
{
    /**
     * @var array<string, array<string>> Property type mappings
     */
    private array $propertyTypes = [
        // Common URL properties
        'url' => ['string'],
        'sameAs' => ['string', 'array'],
        'image' => ['string', 'array'],
        'mainEntityOfPage' => ['string'],
        'additionalType' => ['string'],

        // Text properties
        'name' => ['string'],
        'description' => ['string'],
        'alternateName' => ['string'],
        'disambiguatingDescription' => ['string'],
        'identifier' => ['string', 'integer'],

        // Date properties
        'dateCreated' => ['datetime'],
        'dateModified' => ['datetime'],
        'datePublished' => ['datetime'],
        'birthDate' => ['datetime'],
        'foundingDate' => ['datetime'],

        // Numeric properties
        'wordCount' => ['integer'],
        'numberOfEmployees' => ['integer'],

        // Email and contact
        'email' => ['string'],
        'telephone' => ['string'],

        // Arrays
        'keywords' => ['string', 'array'],
        'potentialAction' => ['array'],
        'knowsAbout' => ['array'],
        'department' => ['array'],
        'subOrganization' => ['array'],
    ];

    public function getRuleId(): string
    {
        return 'property_types';
    }

    public function getDescription(): string
    {
        return 'Validates property types based on Schema.org specifications';
    }

    public function appliesTo(SchemaTypeInterface $schema): bool
    {
        return $schema instanceof AbstractSchemaType;
    }

    public function validate(SchemaTypeInterface $schema): ValidationResult
    {
        if (!$this->appliesTo($schema)) {
            return ValidationResult::success();
        }

        $errors = [];

        foreach ($schema->getProperties() as $property => $value) {
            if (!isset($this->propertyTypes[$property])) {
                continue; // Skip unknown properties (handled by other rules)
            }

            $expectedTypes = $this->propertyTypes[$property];

            if (!$this->isValidType($value, $expectedTypes)) {
                $errors[] = ValidationError::invalidPropertyType(
                    $property,
                    implode('|', $expectedTypes),
                    $value,
                );
            }
        }

        return new ValidationResult($errors);
    }

    public function getSeverity(): string
    {
        return 'error';
    }

    /**
     * Check if a value matches any of the expected types.
     *
     * @param mixed         $value         The value to check
     * @param array<string> $expectedTypes The expected types
     *
     * @return bool True if the value matches any expected type
     */
    private function isValidType(mixed $value, array $expectedTypes): bool
    {
        foreach ($expectedTypes as $type) {
            if ($this->matchesType($value, $type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a value matches a specific type.
     *
     * @param mixed  $value The value to check
     * @param string $type  The type to match against
     *
     * @return bool True if the value matches the type
     */
    private function matchesType(mixed $value, string $type): bool
    {
        return match ($type) {
            'string' => is_string($value),
            'integer' => is_int($value),
            'float' => is_float($value),
            'numeric' => is_numeric($value),
            'boolean' => is_bool($value),
            'array' => is_array($value),
            'datetime' => $value instanceof DateTimeInterface,
            'object' => is_object($value),
            'schema' => $value instanceof SchemaTypeInterface,
            default => false,
        };
    }
}
