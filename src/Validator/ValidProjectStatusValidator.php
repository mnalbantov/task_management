<?php

namespace App\Validator;

use App\Entity\Project;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidProjectStatusValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!in_array($value, Project::$statuses, true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ status }}', $value)
                ->addViolation();
        }
    }
}
