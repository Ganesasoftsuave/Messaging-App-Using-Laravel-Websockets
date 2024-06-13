<?php

namespace App\Exceptions;

use Exception;

/**
 * Class InvalidMessageContentException
 *
 * This exception is thrown when the message content is invalid.
 */
class InvalidMessageContentException extends Exception
{
    /**
     * Constructor for InvalidMessageContentException.
     *
     * @param string $message The exception message to throw.
     * @param int $code The exception code.
     * @param \Exception|null $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = "Invalid message content", $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
