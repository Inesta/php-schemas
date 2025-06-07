<?php

declare(strict_types=1);

namespace Inesta\Schemas\Validation\Rules;

use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Core\AbstractSchemaType;
use Inesta\Schemas\Validation\Interfaces\ValidationRuleInterface;
use Inesta\Schemas\Validation\ValidationError;
use Inesta\Schemas\Validation\ValidationResult;

use function filter_var;
use function in_array;
use function is_string;
use function preg_match;

/**
 * Validates Schema.org compliance for common patterns and requirements.
 */
final class SchemaOrgComplianceRule implements ValidationRuleInterface
{
    public function getRuleId(): string
    {
        return 'schema_org_compliance';
    }

    public function getDescription(): string
    {
        return 'Validates Schema.org compliance for common patterns and requirements';
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
        $warnings = [];

        foreach ($schema->getProperties() as $property => $value) {
            // Validate URLs
            if ($this->isUrlProperty($property) && is_string($value)) {
                if (!$this->isValidUrl($value)) {
                    $errors[] = ValidationError::invalidPropertyValue(
                        $property,
                        $value,
                        'must be a valid URL',
                    );
                }
            }

            // Validate email addresses
            if ($property === 'email' && is_string($value)) {
                if (!$this->isValidEmail($value)) {
                    $errors[] = ValidationError::invalidPropertyValue(
                        $property,
                        $value,
                        'must be a valid email address',
                    );
                }
            }

            // Validate telephone numbers
            if ($property === 'telephone' && is_string($value)) {
                if (!$this->isValidTelephone($value)) {
                    $warnings[] = ValidationError::invalidPropertyValue(
                        $property,
                        $value,
                        'should follow international format (e.g., +1-555-123-4567)',
                    );
                }
            }

            // Validate known properties
            if (!$this->isKnownProperty($schema, $property)) {
                $warnings[] = ValidationError::unknownProperty($property);
            }
        }

        return new ValidationResult($errors, $warnings);
    }

    public function getSeverity(): string
    {
        return 'error';
    }

    /**
     * Check if a property name indicates a URL.
     *
     * @param string $property The property name
     *
     * @return bool True if the property should contain a URL
     */
    private function isUrlProperty(string $property): bool
    {
        $urlProperties = [
            'url',
            'sameAs',
            'mainEntityOfPage',
            'additionalType',
            'image',
            'logo',
        ];

        return in_array($property, $urlProperties, true);
    }

    /**
     * Validate a URL.
     *
     * @param string $url The URL to validate
     *
     * @return bool True if the URL is valid
     */
    private function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate an email address.
     *
     * @param string $email The email to validate
     *
     * @return bool True if the email is valid
     */
    private function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate a telephone number format.
     *
     * @param string $telephone The telephone number to validate
     *
     * @return bool True if the telephone number has a reasonable format
     */
    private function isValidTelephone(string $telephone): bool
    {
        // Basic validation for international format
        return preg_match('/^\+?[\d\s\-\(\)\.]{7,}$/', $telephone) === 1;
    }

    /**
     * Check if a property is known for the given schema type.
     *
     * @param SchemaTypeInterface $schema   The schema instance
     * @param string              $property The property name
     *
     * @return bool True if the property is known
     */
    private function isKnownProperty(SchemaTypeInterface $schema, string $property): bool
    {
        if (!$schema instanceof AbstractSchemaType) {
            return true; // Cannot validate unknown schema types
        }

        return in_array($property, $schema::getValidProperties(), true);
    }
}
