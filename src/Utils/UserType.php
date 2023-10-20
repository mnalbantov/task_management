<?php

namespace App\Utils;

class UserType
{
    public const CLIENT_TYPE = 'client';
    public const COMPANY_TYPE = 'company';

    public static array $types =
        [
            self::CLIENT_TYPE,
            self::COMPANY_TYPE
        ];
}