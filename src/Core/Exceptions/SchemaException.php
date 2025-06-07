<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Exceptions;

use Exception;

/**
 * Base exception for all schema-related errors.
 */
class SchemaException extends Exception
{
    /**
     * Create a new schema exception.
     *
     * @param string         $message  The exception message
     * @param int            $code     The exception code
     * @param Exception|null $previous The previous exception
     */
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
