<?php

namespace App\Dto;

use App\Request\BaseApiRequest;
use App\Validator\AllowedProjectStatus;
use App\Validator\AllowedUserType;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProjectRequest extends BaseApiRequest
{
    private ?int $id;

    #[Assert\NotBlank]
    protected ?string $title = '';

    #[Assert\NotBlank]
    protected ?string $description = '';

    #[Assert\NotBlank]
    #[AllowedProjectStatus]
    protected ?string $status = '';

    #[AllowedUserType]
    protected ?string $userType = '';

    protected ?DateTimeInterface $startDate = null;

    protected ?DateTimeInterface $endDate = null;

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

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(?string $userType): void
    {
        $this->userType = $userType;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTimeInterface $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }
}