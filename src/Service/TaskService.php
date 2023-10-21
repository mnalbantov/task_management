<?php

namespace App\Service;

use App\Dto\TaskRequest;
use App\Entity\Task;
use App\Event\ProjectTaskCreatedEvent;
use App\Event\TaskStateChangedEvent;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Request\WebRequest;
use App\Utils\Constants;
use App\Utils\Helper;
use DateTime;
use DateTimeImmutable;
use Psr\EventDispatcher\EventDispatcherInterface;

class TaskService
{
    private TaskRepository $taskRepository;
    private EventDispatcherInterface $eventDispatcher;
    private ProjectRepository $projectRepository;

    public function __construct(
        TaskRepository $taskRepository,
        ProjectRepository $projectRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->taskRepository = $taskRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->projectRepository = $projectRepository;
    }

    public function getTasks(WebRequest $requestFilters, int $id): array
    {
        $perPage = $requestFilters->getLimitPerPage();
        $page = $requestFilters->getPage();

        return $this->taskRepository->getTasksByProjectId($id, $page, $perPage);
    }

    public function createTask(TaskRequest $taskRequest): Task
    {
        $task = new Task();
        $task->setTitle($taskRequest->getTitle());
        $task->setDescription($taskRequest->getDescription());
        $task->setStatus(Helper::TASK_NEW);
        $task->setEndDate(
            new DateTimeImmutable($taskRequest->getEndDate())
        );
        $task->setProject($this->projectRepository->find($taskRequest->getProjectId()));
        $this->eventDispatcher->dispatch(
            new ProjectTaskCreatedEvent($task),
            Constants::TASK_CREATED
        );

        return $task;
    }

    public function updateTask(Task $task, TaskRequest $taskRequest): void
    {
        if ($task->getProject()->getId() !== $taskRequest->getProjectId()) {
            $task->setProject($this->projectRepository->find($taskRequest->getProjectId()));
        }
        $task->setTitle($taskRequest->getTitle());
        $task->setDescription($taskRequest->getDescription());
        $task->setStatus($taskRequest->getStatus());
        $task->setEndDate(
            new DateTimeImmutable($taskRequest->getEndDate())
        );
        $this->eventDispatcher->dispatch(new TaskStateChangedEvent($task));
    }
}