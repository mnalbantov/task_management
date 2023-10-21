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

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'start_date' => $this->getFormattedStarDate(),
            'end_date' => $this->getFormattedEndDate(),
            'duration' => $this->getDuration(),
            'totalTasks' => $this->getTasks()->count()
        ];
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

    public function updateProjectDuration(): void
    {
        // cannot update if project is failed or completed already
        if (!$this->isFailed() || !$this->isCompleted()) {
            $taskStartDates = $this->tasks->map(function (Task $task) {
                return $task->getStartDate();
            });

            $taskEndDates = $this->tasks->map(function (Task $task) {
                return $task->getEndDate();
            });

            $startDateArray = $taskStartDates->toArray();
            $endDateArray = $taskEndDates->toArray();

            $this->startDate = empty($startDateArray) ? null : min($startDateArray);

            $this->endDate = empty($endDateArray) ? null : max($endDateArray);
        }
    }

    public function updateProjectStatus(): void
    {
        if ($this->isPending()) {
            $this->setStatus(Helper::PROJECT_PENDING);
        }
        if ($this->isFailed()) {
            $this->setStatus(Helper::PROJECT_FAILED);
        }
        if ($this->isNew()) {
            $this->setStatus(Helper::PROJECT_NEW);
        }
        if ($this->isCompleted()) {
            $this->setStatus(Helper::PROJECT_DONE);
        }
        $tasksDeadLines = $this->getTaskDeadLines()->toArray();
        if ($this->isProjectDelayed($tasksDeadLines)) {
            $this->setStatus(Helper::PROJECT_FAILED);
        }
    }

    public function getDuration(): string
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        if ($startDate && $endDate) {
            $interval = $endDate->diff($startDate);
            $days = $interval->format('%a');
            $hours = $interval->format('%h');
            $duration = "$days days and $hours hours";
        } else {
            $duration = 'N/A';
        }

        return $duration;
    }

    private function getFormattedStarDate(): ?string
    {
        if ($this->getStartDate()) {
            return $this->getStartDate()->format('d/M/Y h:m');
        }

        return null;
    }

    private function getFormattedEndDate(): ?string
    {
        if ($this->getEndDate()) {
            return $this->getEndDate()->format('d/M/Y h:m');
        }

        return null;
    }

    private function isCompleted(): bool
    {
        foreach ($this->tasks as $task) {
            if ($task->getStatus() !== Helper::TASK_DONE) {
                return false;
            }
        }

        return true;
    }

    private function isPending(): bool
    {
        if ($this->tasks->isEmpty()) {
            return false;
        }
        //ensure it's not already passed the deadline
        if ($this->isFailed()) {
            return false;
        }

        $inProgressCount = 0;
        foreach ($this->tasks as $task) {
            if ($task->getStatus() === Helper::TASK_IN_PROGRESS || $task->getStatus() !== Helper::TASK_DONE) {
                $inProgressCount++;
            }
        }

        return $inProgressCount / count($this->tasks) > 0.5;
    }

    private function isFailed(): bool
    {
        if ($this->getEndDate() === null) {
            return false;
        }

        $now = new \DateTime();
        $deadline = $this->getEndDate();

        return $now > $deadline;
    }

    private function isNew(): bool
    {
        return $this->tasks->isEmpty();
    }

    private function getTaskDeadLines(): ArrayCollection|Collection
    {
        return $this->tasks->map(function (Task $task) {
            return $task->getEndDate();
        });
    }

    private function isProjectDelayed(array $taskDeadlines): bool
    {
        $currentDate = new \DateTime();
        $latestDeadline = empty($taskDeadlines) ? null : max($taskDeadlines);

        return $latestDeadline !== null && $currentDate > $latestDeadline;
    }

}
