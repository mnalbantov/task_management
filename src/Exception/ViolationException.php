<?php

namespace App\Exception;

use App\Response\Error\ViolationError;
use Throwable;

class ViolationException extends \Exception
{
    private ViolationError $errors;

    public function __construct(
        ViolationError $violationError,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->errors = $violationError;
        parent::__construct($message, $code, $previous);
    }

    public function getViolationErrors(): ViolationError
    {
        return $this->errors;
    }
}