<?php

declare(strict_types=1);

namespace Inesta\Schemas\Validation\Rules;

use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Validation\Interfaces\ValidationRuleInterface;
use Inesta\Schemas\Validation\ValidationError;
use Inesta\Schemas\Validation\ValidationResult;

use function is_string;
use function mb_trim;

/**
 * Validates that properties don't have empty or meaningless values.
 */
final class EmptyValuesRule implements ValidationRuleInterface
{
    public function getRuleId(): string
    {
        return 'empty_values';
    }

    public function getDescription(): string
    {
        return 'Validates that properties don\'t have empty or meaningless values';
    }

    public function appliesTo(SchemaTypeInterface $schema): bool
    {
        return true; // Applies to all schema types
    }

    public function validate(SchemaTypeInterface $schema): ValidationResult
    {
        $warnings = [];

        foreach ($schema->getProperties() as $property => $value) {
            if ($this->isEmpty($value)) {
                $warnings[] = new ValidationError(
                    "Property '{$property}' is empty or has no meaningful value",
                    'EMPTY_PROPERTY',
                    $property,
                    $value,
                );
            }
        }

        return new ValidationResult([], $warnings);
    }

    public function getSeverity(): string
    {
        return 'warning';
    }

    /**
     * Check if a value is considered empty.
     *
     * @param mixed $value The value to check
     *
     * @return bool True if the value is empty
     */
    private function isEmpty(mixed $value): bool
    {
        if ($value === null || $value === '' || $value === []) {
            return true;
        }

        if (is_string($value) && mb_trim($value) === '') {
            return true;
        }

        return false;
    }
}
