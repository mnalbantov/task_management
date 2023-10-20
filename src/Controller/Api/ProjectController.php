<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Exception\ViolationException;
use App\Repository\ProjectRepositoryInterface;
use App\Repository\TaskRepository;
use App\Request\WebRequest;
use App\Response\Error\ViolationResponseHandlerInterface;
use App\Response\NotFoundResponse;
use App\Response\SuccessResponse;
use App\Service\ProjectService;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/project', name: 'project_')]
class ProjectController extends AbstractController
{
    private ProjectRepositoryInterface $projectRepository;
    private TaskRepository $taskRepository;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private ViolationResponseHandlerInterface $violationResponseHandler;
    private ProjectService $projectService;
    private NormalizerInterface $normalizer;
    private TaskService $taskService;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        TaskRepository $taskRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ViolationResponseHandlerInterface $violationResponseHandler,
        ProjectService $projectService,
        NormalizerInterface $normalizer,
        TaskService $taskService,
    ) {
        $this->projectRepository = $projectRepository;
        $this->taskRepository = $taskRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->violationResponseHandler = $violationResponseHandler;
        $this->projectService = $projectService;
        $this->normalizer = $normalizer;
        $this->taskService = $taskService;
    }

    #[Route('/{id<\d+>}/tasks', name: 'tasks', methods: ['GET'])]
    public function tasks(Request $request, int $id): SuccessResponse
    {
        $project = $this->projectRepository->find($id);
        if (!$project) {
            return new NotFoundResponse();
        }

        $tasks = $this->taskService->getTasks(WebRequest::getRequestFilters($request), $id);

        return new SuccessResponse($tasks);
    }

    /**
     * @throws ViolationException
     */
    #[Route('/{id<\d+>}/tasks/create', name: 'task_create', methods: ['POST'])]
    public function createTask(Request $request, int $id): JsonResponse
    {
        $project = $this->projectRepository->find($id);
        if (!$project) {
            return new NotFoundResponse();
        }
        $task = $this->deserializeTask($request);
        $task = $this->taskService->createTask($task, $project);

        return new SuccessResponse($task);
    }

    private function deserializeTask(Request $request): Task
    {
        return $this->serializer->deserialize(
            $request->getContent(),
            Task::class,
            'json'
        );
    }

}