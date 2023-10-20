<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ErrorResponse extends JsonResponse
{
    public function __construct(
        array $errors = [],
        int $code = -1,
        array $headers = [],
        int $httpStatusCode = Response::HTTP_OK,
        bool $json = false
    ) {
        $data = [
            'code' => $code,
            'data' => [],
            'validation_errors' => $errors
        ];
        parent::__construct($data, $httpStatusCode, $headers, $json);
    }
}
