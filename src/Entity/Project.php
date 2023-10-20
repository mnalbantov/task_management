<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use App\Utils\Helper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project implements \JsonSerializable
{
    public const NEW = 'new';
    public const PENDING = 'pending';
    public const FAILED = 'failed';
    public const DONE = 'done';

    public static array $statuses = [
        self::NEW,
        self::PENDING,
        self::FAILED,
        self::DONE,
    ];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', options: ['enum' => 'project_status'])]
    private string $status = self::NEW;

    #[ORM\Column(type: 'string', options: ['enum' => 'user_type'])]
    private string $userType;

    #[ORM\Column]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Task::class)]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): void
    {
        $this->userType = $userType;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setProject($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }

        return $this;
    }

    public function updateProjectStatus()
    {
        $taskStatuses = $this->getTasks()->map(function (Task $task) {
            return $task->getStatus();
        });
        $taskDeadlines = $this->getTasks()->map(function (Task $task) {
            return $task->getEndDate();
        });
        if ($taskStatuses->contains(Helper::PROJECT_FAILED)) {
            $this->status = Helper::PROJECT_FAILED;
        } elseif ($taskStatuses->contains(Helper::PROJECT_DONE)) {
            $this->status = Helper::PROJECT_DONE;
        } else {
            $this->status = Helper::PROJECT_PENDING;
        }
    }

    private function isProjectDelayed($taskDeadlines): bool
    {
        // Determine if the project is delayed based on your business rules
        // For example, check if the current date is past the project's expected end date.
        $currentDate = new \DateTime();
        $latestDeadline = $taskDeadlines->isEmpty() ? null : $taskDeadlines->max();

        return $latestDeadline !== null && $currentDate > $latestDeadline;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
