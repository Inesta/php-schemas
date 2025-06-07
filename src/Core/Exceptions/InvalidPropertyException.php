<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Exceptions;

use Exception;

/**
 * Exception thrown when an invalid property is accessed or set.
 */
final class InvalidPropertyException extends SchemaException
{
    public function __construct(
        string $property,
        string $schemaType,
        string $reason = '',
        int $code = 0,
        ?Exception $previous = null,
    ) {
        $message = "Invalid property '{$property}' for schema type '{$schemaType}'";

        if ($reason !== '') {
            $message .= ": {$reason}";
        }

        parent::__construct($message, $code, $previous);
    }
}
