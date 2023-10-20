<?php

namespace App\Service;

use App\Entity\Project;
use App\Event\ProjectCreatedEvent;
use App\Repository\ProjectRepositoryInterface;
use App\Repository\TaskRepository;
use App\Response\Error\ViolationResponseHandlerInterface;
use App\Utils\Constants;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProjectService
{
    private ProjectRepositoryInterface $repository;
    private EventDispatcherInterface $eventDispatcher;
    private ValidatorInterface $validator;
    private ViolationResponseHandlerInterface $violationResponseHandler;
    private TaskRepository $taskRepository;

    public function __construct(
        ProjectRepositoryInterface $repository,
        EventDispatcherInterface $eventDispatcher,
        ValidatorInterface $validator,
        ViolationResponseHandlerInterface $violationResponseHandler,
        TaskRepository $taskRepository
    ) {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
        $this->validator = $validator;
        $this->violationResponseHandler = $violationResponseHandler;
        $this->taskRepository = $taskRepository;
    }

    public function createProject(Project $project): void
    {
        $this->eventDispatcher->dispatch(new ProjectCreatedEvent($project), Constants::PROJECT_CREATED);
    }
}