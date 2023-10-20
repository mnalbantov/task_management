<?php

namespace App\Service;

use App\Dto\TaskDto;
use App\Entity\Project;
use App\Entity\Task;
use App\Event\ProjectTaskCreatedEvent;
use App\Event\TaskStateChangedEvent;
use App\Exception\ViolationException;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Request\WebRequest;
use App\Response\Error\ViolationError;
use App\Utils\Constants;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskService
{
    private TaskRepository $taskRepository;
    private ValidatorInterface $validator;
    private EventDispatcherInterface $eventDispatcher;
    private ProjectRepository $projectRepository;

    public function __construct(
        TaskRepository $taskRepository,
        ProjectRepository $projectRepository,
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->taskRepository = $taskRepository;
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
        $this->projectRepository = $projectRepository;
    }

    public function getTasks(WebRequest $requestFilters, int $id): array
    {
        $perPage = $requestFilters->getLimitPerPage();
        $page = $requestFilters->getPage();

        return $this->taskRepository->getTasksByProjectId($id, $page, $perPage);
    }

    /**
     * @throws ViolationException
     */
    public function createTask(Task $task, Project $project): Task
    {
        $task->setProject($project);
        $violations = $this->validator->validate($task);
        if (count($violations) > 0) {
            throw new ViolationException(new ViolationError($violations));
        }
        $this->eventDispatcher->dispatch(new ProjectTaskCreatedEvent($task), Constants::TASK_CREATED);

        return $task;
    }

    /**
     * @throws ViolationException
     */
    public function updateTask(Task $task, TaskDto $dto): void
    {
        if ($dto->getProjectId()) {
            $project = $this->projectRepository->find($dto->getProjectId());
        }
        $task->setProject($project ?? $task->getProject());
        $task->setTitle($dto->getTitle());
        $task->setDescription($dto->getDescription());
        $task->setEndDate($dto->getEndDate());

        $violations = $this->validator->validate($task);
        if (count($violations) > 0) {
            throw new ViolationException(new ViolationError($violations));
        }

        $this->eventDispatcher->dispatch(new TaskStateChangedEvent($task), Constants::TASK_UPDATED);
    }
}