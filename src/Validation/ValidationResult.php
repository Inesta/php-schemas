<?php

declare(strict_types=1);

namespace Inesta\Schemas\Validation;

use JsonSerializable;

use function array_map;
use function count;

/**
 * Represents the result of schema validation.
 *
 * Contains information about validation errors, warnings, and overall status.
 */
final readonly class ValidationResult implements JsonSerializable
{
    /**
     * @param array<ValidationError> $errors   Validation errors
     * @param array<ValidationError> $warnings Validation warnings
     */
    public function __construct(
        private array $errors = [],
        private array $warnings = [],
    ) {}

    /**
     * Check if the validation passed (no errors).
     *
     * @return bool True if validation passed, false otherwise
     */
    public function isValid(): bool
    {
        return $this->errors === [];
    }

    /**
     * Check if there are any validation errors.
     *
     * @return bool True if there are errors, false otherwise
     */
    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    /**
     * Check if there are any validation warnings.
     *
     * @return bool True if there are warnings, false otherwise
     */
    public function hasWarnings(): bool
    {
        return $this->warnings !== [];
    }

    /**
     * Get all validation errors.
     *
     * @return array<ValidationError> The validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get all validation warnings.
     *
     * @return array<ValidationError> The validation warnings
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * Get the total number of errors.
     *
     * @return int The error count
     */
    public function getErrorCount(): int
    {
        return count($this->errors);
    }

    /**
     * Get the total number of warnings.
     *
     * @return int The warning count
     */
    public function getWarningCount(): int
    {
        return count($this->warnings);
    }

    /**
     * Get all error messages as strings.
     *
     * @return array<string> The error messages
     */
    public function getErrorMessages(): array
    {
        return array_map(
            static fn (ValidationError $error): string => $error->getMessage(),
            $this->errors,
        );
    }

    /**
     * Get all warning messages as strings.
     *
     * @return array<string> The warning messages
     */
    public function getWarningMessages(): array
    {
        return array_map(
            static fn (ValidationError $warning): string => $warning->getMessage(),
            $this->warnings,
        );
    }

    /**
     * Merge this validation result with another.
     *
     * @param ValidationResult $other The other validation result
     *
     * @return ValidationResult A new merged validation result
     */
    public function merge(self $other): self
    {
        return new self(
            [...$this->errors, ...$other->errors],
            [...$this->warnings, ...$other->warnings],
        );
    }

    /**
     * Convert to array representation for JSON serialization.
     *
     * @return array<string, mixed> The array representation
     */
    public function jsonSerialize(): array
    {
        return [
            'valid' => $this->isValid(),
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'errorCount' => $this->getErrorCount(),
            'warningCount' => $this->getWarningCount(),
        ];
    }

    /**
     * Create a successful validation result.
     *
     * @return self A successful validation result
     */
    public static function success(): self
    {
        return new self();
    }

    /**
     * Create a validation result with errors.
     *
     * @param array<ValidationError> $errors The validation errors
     *
     * @return self A validation result with errors
     */
    public static function withErrors(array $errors): self
    {
        return new self($errors);
    }

    /**
     * Create a validation result with warnings.
     *
     * @param array<ValidationError> $warnings The validation warnings
     *
     * @return self A validation result with warnings
     */
    public static function withWarnings(array $warnings): self
    {
        return new self([], $warnings);
    }

    /**
     * Create a validation result with both errors and warnings.
     *
     * @param array<ValidationError> $errors   The validation errors
     * @param array<ValidationError> $warnings The validation warnings
     *
     * @return self A validation result with errors and warnings
     */
    public static function withErrorsAndWarnings(array $errors, array $warnings): self
    {
        return new self($errors, $warnings);
    }
}
