<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Exceptions;

use Exception;
use Inesta\Schemas\Validation\ValidationResult;

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
        parent::__construct($message, $code, $previous);
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
}
