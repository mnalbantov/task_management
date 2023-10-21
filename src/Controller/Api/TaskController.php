<?php

namespace App\Controller\Api;

use App\Dto\CreateTaskRequest;
use App\Dto\UpdateTaskRequest;
use App\Exception\ViolationException;
use App\Repository\TaskRepository;
use App\Response\NotFoundResponse;
use App\Response\SuccessResponse;
use App\Service\TaskService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tasks', name: 'tasks_')]
class TaskController extends BaseApiController
{
    private TaskRepository $taskRepository;
    private TaskService $taskService;

    public function __construct(
        TaskRepository $taskRepository,
        TaskService $taskService,
    ) {
        $this->taskRepository = $taskRepository;
        $this->taskService = $taskService;
    }

    /**
     * @throws ViolationException
     */
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(CreateTaskRequest $taskRequest): JsonResponse
    {
        $this->validateRequest($taskRequest);
        $task = $this->taskService->createTask($taskRequest);

        return new SuccessResponse($task);
    }

    /**
     * @throws ViolationException
     */
    #[Route('/{id<\d+>}/update', name: 'update', methods: ['PATCH'])]
    public function update(UpdateTaskRequest $taskRequest, int $id): JsonResponse
    {
        $task = $this->taskRepository->getActiveTaskById($id);
        if (!$task) {
            return new NotFoundResponse();
        }
        $this->validateRequest($taskRequest);
        $task = $this->taskService->updateTask($task, $taskRequest);

        return new SuccessResponse($task);
    }

    #[Route('/{id<\d+>}/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $task = $this->taskRepository->getActiveTaskById($id);
        if (!$task) {
            return new NotFoundResponse();
        }
        $this->taskService->deleteTask($task);

        return new SuccessResponse();
    }
}