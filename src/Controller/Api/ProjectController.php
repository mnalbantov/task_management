<?php

namespace App\Controller\Api;

use App\Dto\CreateProjectRequest;
use App\Exception\ViolationException;
use App\Repository\ProjectRepositoryInterface;
use App\Request\WebRequest;
use App\Response\NotFoundResponse;
use App\Response\SuccessResponse;
use App\Service\ProjectService;
use App\Service\TaskService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projects', name: 'projects_')]
class ProjectController extends BaseApiController
{
    private ProjectRepositoryInterface $projectRepository;
    private ProjectService $projectService;
    private TaskService $taskService;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        ProjectService $projectService,
        TaskService $taskService,
    ) {
        $this->projectRepository = $projectRepository;
        $this->projectService = $projectService;
        $this->taskService = $taskService;
    }

    #[Route('/', name: 'list', methods: ['GET'])]
    public function index(Request $request): SuccessResponse
    {
        return new SuccessResponse(
            $this->projectService
                ->getProjects(
                    WebRequest::getRequestFilters($request)
                )
        );
    }

    #[Route('/{id<\d+>}', name: 'view', methods: ['GET'])]
    public function view(int $id): JsonResponse
    {
        $project = $this->projectRepository->find($id);
        if (!$project) {
            return new NotFoundResponse();
        }

        return new SuccessResponse($project);
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
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(CreateProjectRequest $projectRequest): JsonResponse
    {
        $this->validateRequest($projectRequest);
        $project = $this->projectService->createProject($projectRequest);

        return new SuccessResponse($project);
    }

}