<?php

namespace App\Response\Error;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;

class ViolationResponseHandler implements ViolationResponseHandlerInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function handleViolationResponse(ViolationError $violationError): array
    {
        $violations = $violationError->getViolations();

        $errors = [];
        /**@var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $errors;
    }
}