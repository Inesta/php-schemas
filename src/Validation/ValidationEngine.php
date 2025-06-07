<?php

declare(strict_types=1);

namespace Inesta\Schemas\Validation;

use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Contracts\ValidatorInterface;
use Inesta\Schemas\Validation\Interfaces\ValidationRuleInterface;
use Inesta\Schemas\Validation\Rules\EmptyValuesRule;
use Inesta\Schemas\Validation\Rules\PropertyTypesRule;
use Inesta\Schemas\Validation\Rules\RequiredPropertiesRule;
use Inesta\Schemas\Validation\Rules\SchemaOrgComplianceRule;

use function array_filter;
use function array_map;
use function array_values;

/**
 * Advanced validation engine that supports pluggable rules.
 *
 * The ValidationEngine manages a collection of validation rules and applies
 * them to schema objects, providing detailed validation results.
 */
final class ValidationEngine implements ValidatorInterface
{
    /**
     * @var array<ValidationRuleInterface> The registered validation rules
     */
    private array $rules = [];

    /**
     * @var bool Whether to stop validation on first error
     */
    private bool $stopOnFirstError = false;

    public function __construct()
    {
        $this->registerDefaultRules();
    }

    /**
     * Register a validation rule.
     *
     * @param ValidationRuleInterface $rule The rule to register
     *
     * @return static The validation engine for method chaining
     */
    public function addRule(ValidationRuleInterface $rule): static
    {
        $this->rules[$rule->getRuleId()] = $rule;

        return $this;
    }

    /**
     * Remove a validation rule.
     *
     * @param string $ruleId The rule identifier to remove
     *
     * @return static The validation engine for method chaining
     */
    public function removeRule(string $ruleId): static
    {
        unset($this->rules[$ruleId]);

        return $this;
    }

    /**
     * Check if a rule is registered.
     *
     * @param string $ruleId The rule identifier
     *
     * @return bool True if the rule is registered
     */
    public function hasRule(string $ruleId): bool
    {
        return isset($this->rules[$ruleId]);
    }

    /**
     * Get a specific rule by ID.
     *
     * @param string $ruleId The rule identifier
     *
     * @return ValidationRuleInterface|null The rule or null if not found
     */
    public function getRule(string $ruleId): ?ValidationRuleInterface
    {
        return $this->rules[$ruleId] ?? null;
    }

    /**
     * Get all registered rules.
     *
     * @return array<ValidationRuleInterface> The registered rules
     */
    public function getRules(): array
    {
        return array_values($this->rules);
    }

    /**
     * Get all applicable rules for a schema.
     *
     * @param SchemaTypeInterface $schema The schema to check
     *
     * @return array<ValidationRuleInterface> The applicable rules
     */
    public function getApplicableRules(SchemaTypeInterface $schema): array
    {
        return array_filter(
            $this->rules,
            static fn (ValidationRuleInterface $rule): bool => $rule->appliesTo($schema),
        );
    }

    /**
     * Set whether to stop validation on the first error.
     *
     * @param bool $stopOnFirstError Whether to stop on first error
     *
     * @return static The validation engine for method chaining
     */
    public function setStopOnFirstError(bool $stopOnFirstError): static
    {
        $this->stopOnFirstError = $stopOnFirstError;

        return $this;
    }

    /**
     * Clear all registered rules.
     *
     * @return static The validation engine for method chaining
     */
    public function clearRules(): static
    {
        $this->rules = [];

        return $this;
    }

    public function validate(SchemaTypeInterface $schema): ValidationResult
    {
        $allErrors = [];
        $allWarnings = [];

        $applicableRules = $this->getApplicableRules($schema);

        foreach ($applicableRules as $rule) {
            $result = $rule->validate($schema);

            if ($rule->getSeverity() === 'error') {
                $allErrors = [...$allErrors, ...$result->getErrors()];

                if ($this->stopOnFirstError && $result->hasErrors()) {
                    break;
                }
            } else {
                $allWarnings = [...$allWarnings, ...$result->getErrors()];
            }

            $allWarnings = [...$allWarnings, ...$result->getWarnings()];
        }

        return new ValidationResult($allErrors, $allWarnings);
    }

    public function getSupportedRules(): array
    {
        return array_map(
            static fn (ValidationRuleInterface $rule): string => $rule->getRuleId(),
            $this->rules,
        );
    }

    /**
     * Register the default validation rules.
     */
    private function registerDefaultRules(): void
    {
        $this->addRule(new RequiredPropertiesRule());
        $this->addRule(new PropertyTypesRule());
        $this->addRule(new EmptyValuesRule());
        $this->addRule(new SchemaOrgComplianceRule());
    }
}
