<?php

namespace App\Utils;

class Helper
{
    public const CLIENT_TYPE = 'client';
    public const COMPANY_TYPE = 'company';

    public const TASK_NEW = 'new';
    public const TASK_IN_PROGRESS = 'in_progress';
    public const TASK_TESTING = 'testing';
    public const TASK_DONE = 'done';

    public const PROJECT_NEW = 'new';
    public const PROJECT_PENDING = 'pending';
    public const PROJECT_FAILED = 'failed';
    public const PROJECT_DONE = 'done';

    public static array $userTypes =
        [
            self::CLIENT_TYPE,
            self::COMPANY_TYPE,
        ];

    public static array $taskStatuses = [
        self::TASK_NEW,
        self::TASK_IN_PROGRESS,
        self::TASK_TESTING,
        self::TASK_DONE,
    ];

    public static array $projectStatuses = [
        self::PROJECT_NEW,
        self::PROJECT_PENDING,
        self::PROJECT_FAILED,
        self::PROJECT_DONE,
    ];

    public static function createDateTime($dateTimeString): \DateTime|bool
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $dateTimeString);
    }
}
