<?php

namespace App\Response\Error;

interface ViolationResponseHandlerInterface
{
    public function handleViolationResponse(ViolationError $violationError);
}
