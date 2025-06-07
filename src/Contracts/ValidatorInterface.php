<?php

declare(strict_types=1);

namespace Inesta\Schemas\Contracts;

use Inesta\Schemas\Validation\ValidationResult;

/**
 * Interface for schema validators.
 *
 * Validators are responsible for checking that schemas conform to
 * Schema.org specifications and other validation rules.
 */
interface ValidatorInterface
{
    /**
     * Validate a schema type.
     *
     * @param SchemaTypeInterface $schema The schema to validate
     *
     * @return ValidationResult The validation result
     */
    public function validate(SchemaTypeInterface $schema): ValidationResult;

    /**
     * Get the validation rules that this validator supports.
     *
     * @return array<string> Array of rule identifiers
     */
    public function getSupportedRules(): array;
}
