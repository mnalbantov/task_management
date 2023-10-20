<?php

namespace App\Controller\Api;

use App\Dto\TaskRequest;
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
    public function createTask(TaskRequest $taskRequest): JsonResponse
    {
        $this->validateRequest($taskRequest);
        $task = $this->taskService->createTask($taskRequest);

        return new SuccessResponse($task);
    }

    /**
     * @throws ViolationException
     */
    #[Route('/{id<\d+>}/update', name: 'update', methods: ['PATCH'])]
    public function updateTask(TaskRequest $taskRequest, int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        if (!$task) {
            return new NotFoundResponse();
        }
        $this->validateRequest($taskRequest);
        $this->taskService->updateTask($task, $taskRequest);

        return new SuccessResponse();
    }
}