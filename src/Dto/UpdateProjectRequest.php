<?php

namespace App\Dto;

use App\Validator\AllowedProjectStatus;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateProjectRequest extends CreateProjectRequest
{
    #[Assert\NotBlank]
    #[AllowedProjectStatus]
    protected ?string $status = '';

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }
}