<?php

namespace App\Response;

use App\Utils\Constants;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SuccessResponse extends JsonResponse
{
    public function __construct(
        mixed $data = [],
        int $status = Response::HTTP_OK,
        int $code = 0,
        array $headers = [],
        string $message = Constants::SUCCESS,
        bool $json = false
    ) {
        $data = ['code' => $code, 'data' => $data, 'status' => $message];

        $data = array_merge($data);
        parent::__construct($data, $status, $headers, $json);
    }
}

