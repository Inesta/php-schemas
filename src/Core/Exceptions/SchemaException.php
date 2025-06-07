<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Exceptions;

use Exception;

use function array_key_exists;
use function array_map;
use function array_slice;
use function count;
use function get_class;
use function implode;
use function is_array;
use function is_object;
use function is_string;
use function json_encode;
use function sprintf;

/**
 * Base exception for all schema-related errors.
 */
class SchemaException extends Exception
{
    /**
     * Create a new schema exception.
     *
     * @param string               $message  The exception message
     * @param int                  $code     The exception code
     * @param Exception|null       $previous The previous exception
     * @param array<string, mixed> $context  Additional context information
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Exception $previous = null,
        private readonly array $context = [],
    ) {
        // Add helpful context to the message
        if ($this->context !== []) {
            $message .= "\n\nAdditional context:";
            foreach ($this->context as $key => $value) {
                $message .= sprintf("\n  %s: %s", $key, $this->formatContextValue($value));
            }
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the context information for this exception.
     *
     * @return array<string, mixed> The context data
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get a specific context value.
     *
     * @param string $key     The context key
     * @param mixed  $default The default value if key doesn't exist
     *
     * @return mixed The context value
     */
    public function getContextValue(string $key, mixed $default = null): mixed
    {
        return $this->context[$key] ?? $default;
    }

    /**
     * Check if a context key exists.
     *
     * @param string $key The context key
     *
     * @return bool True if the key exists
     */
    public function hasContext(string $key): bool
    {
        return array_key_exists($key, $this->context);
    }

    /**
     * Format a context value for display.
     *
     * @param mixed $value The value to format
     *
     * @return string Formatted value
     */
    private function formatContextValue(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            if ($value === []) {
                return '[]';
            }

            return '[' . implode(', ', array_map(
                static fn ($item): string => is_string($item) ? $item : (json_encode($item) !== false ? json_encode($item) : 'null'),
                array_slice($value, 0, 3),
            )) . (count($value) > 3 ? '...' : '') . ']';
        }

        if (is_object($value)) {
            return sprintf('[%s object]', get_class($value));
        }

        return json_encode($value) !== false ? json_encode($value) : 'null';
    }

    /**
     * Create an exception for unknown schema types.
     *
     * @param string $type The unknown schema type
     *
     * @return self The exception instance
     */
    public static function unknownType(string $type): self
    {
        return new self(
            sprintf('Unknown schema type: %s', $type),
            0,
            null,
            [
                'type' => $type,
                'suggestion' => 'Check Schema.org documentation for valid types',
                'reference' => 'https://schema.org/docs/schemas.html',
            ],
        );
    }

    /**
     * Create an exception for invalid schema context.
     *
     * @param string $context The invalid context
     *
     * @return self The exception instance
     */
    public static function invalidContext(string $context): self
    {
        return new self(
            sprintf('Invalid schema context: %s', $context),
            0,
            null,
            [
                'context' => $context,
                'suggestion' => 'Use a valid schema context URL like https://schema.org',
                'valid_contexts' => ['https://schema.org', 'https://schema.org/'],
            ],
        );
    }
}
