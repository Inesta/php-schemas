<?php

declare(strict_types=1);

namespace Inesta\Schemas\Validation;

use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Contracts\ValidatorInterface;
use Inesta\Schemas\Core\AbstractSchemaType;

/**
 * Default schema validator.
 *
 * Validates schemas against Schema.org specifications and other rules.
 */
final class Validator implements ValidatorInterface
{
    public function validate(SchemaTypeInterface $schema): ValidationResult
    {
        $errors = [];
        $warnings = [];

        // Validate required properties
        if ($schema instanceof AbstractSchemaType) {
            foreach ($schema::getRequiredProperties() as $property) {
                if (!$schema->hasProperty($property)) {
                    $errors[] = ValidationError::requiredProperty($property);
                }
            }

            // Validate property types and values
            foreach ($schema->getProperties() as $property => $value) {
                // Basic validation - can be extended with more specific rules
                if ($value === null || $value === '' || $value === []) {
                    $warnings[] = new ValidationError(
                        "Property '{$property}' is empty",
                        'EMPTY_PROPERTY',
                        $property,
                        $value,
                    );
                }
            }
        }

        return new ValidationResult($errors, $warnings);
    }

    public function getSupportedRules(): array
    {
        return [
            'required_properties',
            'property_types',
            'empty_values',
        ];
    }
}
