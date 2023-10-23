<?php

namespace App\Controller\Api;

use App\Exception\ViolationException;
use App\Request\BaseApiRequest;
use App\Response\Error\ViolationError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseApiController extends AbstractController
{
    /**
     * @throws ViolationException
     */
    protected function validateRequest(BaseApiRequest $apiRequest)
    {
        $violations = $apiRequest->validate();
        if (count($violations) > 0) {
            throw new ViolationException(new ViolationError($violations));
        }
    }
}
