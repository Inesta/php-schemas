<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Exceptions;

use Exception;
use Inesta\Schemas\Validation\ValidationResult;

use function array_slice;
use function count;
use function implode;
use function sprintf;

/**
 * Exception thrown when schema validation fails.
 */
final class ValidationException extends SchemaException
{
    public function __construct(
        private readonly ValidationResult $validationResult,
        string $message = 'Schema validation failed',
        int $code = 0,
        ?Exception $previous = null,
    ) {
        // Enhance the message with validation details
        $errorCount = count($this->validationResult->getErrors());
        $enhancedMessage = sprintf(
            '%s (%d validation error%s)',
            $message,
            $errorCount,
            $errorCount === 1 ? '' : 's',
        );

        $errorMessages = $this->validationResult->getErrorMessages();
        if ($errorMessages !== []) {
            $enhancedMessage .= "\n\nValidation errors:\n- " . implode("\n- ", array_slice($errorMessages, 0, 5));
            if (count($errorMessages) > 5) {
                $enhancedMessage .= sprintf("\n... and %d more error(s)", count($errorMessages) - 5);
            }
        }

        $enhancedMessage .= "\n\nSuggestion: Review all required properties and ensure they have valid values according to Schema.org specifications.";
        $enhancedMessage .= "\nUse SchemaDebugger to get detailed information about validation issues.";

        parent::__construct($enhancedMessage, $code, $previous);
    }

    /**
     * Get the validation result that caused this exception.
     *
     * @return ValidationResult The validation result
     */
    public function getValidationResult(): ValidationResult
    {
        return $this->validationResult;
    }

    /**
     * Get all validation error messages.
     *
     * @return array<string> The error messages
     */
    public function getErrorMessages(): array
    {
        return $this->validationResult->getErrorMessages();
    }

    /**
     * Get the number of validation errors.
     *
     * @return int The error count
     */
    public function getErrorCount(): int
    {
        return count($this->validationResult->getErrors());
    }

    /**
     * Check if there are multiple validation errors.
     *
     * @return bool True if multiple errors exist
     */
    public function hasMultipleErrors(): bool
    {
        return $this->getErrorCount() > 1;
    }

    /**
     * Get a summary of validation errors grouped by type.
     *
     * @return array<string, int> Error counts by type
     */
    public function getErrorSummary(): array
    {
        $summary = [];
        foreach ($this->validationResult->getErrors() as $error) {
            $type = $error->getType() ?? 'unknown';
            $summary[$type] = ($summary[$type] ?? 0) + 1;
        }

        return $summary;
    }

    /**
     * Get detailed error information for debugging.
     *
     * @return array<array<string, mixed>> Detailed error information
     */
    public function getDetailedErrors(): array
    {
        $details = [];
        foreach ($this->validationResult->getErrors() as $error) {
            $details[] = [
                'property' => $error->getProperty(),
                'message' => $error->getMessage(),
                'value' => $error->getValue(),
                'type' => $error->getType(),
            ];
        }

        return $details;
    }
}
