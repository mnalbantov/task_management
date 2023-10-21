<?php

namespace App\Dto;

use App\Request\BaseApiRequest;
use App\Validator\AllowedUserType;
use Symfony\Component\Validator\Constraints as Assert;

class CreateProjectRequest extends BaseApiRequest
{
    private ?int $id;

    #[Assert\NotBlank]
    protected ?string $title = '';

    #[Assert\NotBlank]
    protected ?string $description = '';

    #[AllowedUserType]
    protected ?string $userType = '';

    #[Assert\DateTime]
    protected ?string $startDate;

    #[Assert\DateTime]
    #[Assert\GreaterThan(propertyPath: 'startDate')]
    protected ?string $endDate;

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

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(?string $userType): void
    {
        $this->userType = $userType;
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