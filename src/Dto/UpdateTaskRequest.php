<?php

namespace App\Dto;

use App\Validator\AllowedTaskStatus;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateTaskRequest extends CreateTaskRequest
{
    #[Assert\NotBlank]
    #[AllowedTaskStatus]
    protected ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }
}