<?php
namespace App\Exceptions;

use Exception;

class InvalidMessageContentException extends Exception
{
    public function __construct($message = "Invalid message content", $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}