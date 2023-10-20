<?php

namespace App\Controller\Api;

use App\Dto\TaskDto;
use App\Entity\Task;
use App\Exception\ViolationException;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Response\NotFoundResponse;
use App\Response\SuccessResponse;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/task', name: 'task_')]
class TaskController extends AbstractController
{

    private TaskRepository $taskRepository;
    private SerializerInterface $serializer;
    private TaskService $taskService;
    private ProjectRepository $projectRepository;

    public function __construct(
        TaskRepository $taskRepository,
        SerializerInterface $serializer,
        TaskService $taskService,
        ProjectRepository $projectRepository
    ) {
        $this->taskRepository = $taskRepository;
        $this->serializer = $serializer;
        $this->taskService = $taskService;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @throws ViolationException
     */
    #[Route('/{id<\d+>}/update', name: 'task_update', methods: ['PATCH'])]
    public function updateTask(Request $request, int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        if (!$task) {
            return new NotFoundResponse();
        }
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            TaskDto::class,
            'json'
        );
        $this->taskService->updateTask($task, $dto);

        return new SuccessResponse();
    }
}