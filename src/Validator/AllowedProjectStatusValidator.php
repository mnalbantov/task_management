<?php

namespace App\Validator;

use App\Utils\Helper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AllowedProjectStatusValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!in_array($value, Helper::$projectStatuses)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
