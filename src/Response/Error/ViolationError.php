<?php

namespace App\Response\Error;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ViolationError
{
    private ConstraintViolationListInterface $violations;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
