<?php

declare(strict_types=1);

namespace Inesta\Schemas\Validation\Interfaces;

use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Validation\ValidationResult;

/**
 * Interface for validation rules.
 *
 * Validation rules implement specific checks that can be applied to schema objects.
 */
interface ValidationRuleInterface
{
    /**
     * Get the unique identifier for this rule.
     *
     * @return string The rule identifier
     */
    public function getRuleId(): string;

    /**
     * Get a human-readable description of this rule.
     *
     * @return string The rule description
     */
    public function getDescription(): string;

    /**
     * Check if this rule applies to the given schema type.
     *
     * @param SchemaTypeInterface $schema The schema to check
     *
     * @return bool True if the rule applies, false otherwise
     */
    public function appliesTo(SchemaTypeInterface $schema): bool;

    /**
     * Validate the schema against this rule.
     *
     * @param SchemaTypeInterface $schema The schema to validate
     *
     * @return ValidationResult The validation result
     */
    public function validate(SchemaTypeInterface $schema): ValidationResult;

    /**
     * Get the severity level of violations of this rule.
     *
     * @return string The severity level ('error' or 'warning')
     */
    public function getSeverity(): string;
}
