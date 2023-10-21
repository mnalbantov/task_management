<?php

namespace App\Dto;

use App\Request\BaseApiRequest;
use App\Validator\AllowedTaskStatus;
use App\Validator\ProjectIsActive;
use Symfony\Component\Validator\Constraints as Assert;

class TaskRequest extends BaseApiRequest
{
    private ?int $id;

    #[Assert\NotBlank]
    protected ?string $title = '';

    #[Assert\NotBlank]
    protected ?string $description = '';

    #[Assert\NotBlank]
    #[AllowedTaskStatus]
    protected ?string $status = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[ProjectIsActive]
    protected ?int $projectId = null;

    #[Assert\DateTime]
    protected ?string $startDate = null;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    #[Assert\GreaterThan(propertyPath: 'startDate')]
    protected string $endDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getProjectId(): ?int
    {
        return $this->projectId;
    }

    public function setProjectId(?int $projectId): void
    {
        $this->projectId = $projectId;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function setStartDate(?string $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function setEndDate(?string $endDate): void
    {
        $this->endDate = $endDate;
    }

}