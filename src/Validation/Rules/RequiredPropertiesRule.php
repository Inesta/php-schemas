<?php

declare(strict_types=1);

namespace Inesta\Schemas\Validation\Rules;

use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Core\AbstractSchemaType;
use Inesta\Schemas\Validation\Interfaces\ValidationRuleInterface;
use Inesta\Schemas\Validation\ValidationError;
use Inesta\Schemas\Validation\ValidationResult;

/**
 * Validates that all required properties are present.
 */
final class RequiredPropertiesRule implements ValidationRuleInterface
{
    public function getRuleId(): string
    {
        return 'required_properties';
    }

    public function getDescription(): string
    {
        return 'Validates that all required properties are present';
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

        /** @var AbstractSchemaType $schema */
        $errors = [];

        foreach ($schema::getRequiredProperties() as $property) {
            if (!$schema->hasProperty($property)) {
                $errors[] = ValidationError::requiredProperty($property);
            }
        }

        return new ValidationResult($errors);
    }

    public function getSeverity(): string
    {
        return 'error';
    }
}
