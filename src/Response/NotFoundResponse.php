<?php

namespace App\Response;

use App\Utils\Constants;
use Symfony\Component\HttpFoundation\Response;

class NotFoundResponse extends SuccessResponse
{
    public function __construct(
        mixed $data = [],
        int $status = Response::HTTP_OK,
        int $code = -1,
        array $headers = [],
        string $message = Constants::NOT_FOUND,
        bool $json = false
    ) {
        parent::__construct($data, $status, $code, $headers, $message, $json);
    }
}
