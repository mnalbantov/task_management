<?php

namespace App\Controller\Api;

use App\Dto\CreateProjectRequest;
use App\Exception\ViolationException;
use App\Repository\ProjectRepositoryInterface;
use App\Request\WebRequest;
use App\Response\Formatter\ResponseFormatterInterface;
use App\Response\NotFoundResponse;
use App\Response\PaginatedApiFormatter;
use App\Response\SuccessResponse;
use App\Service\ProjectService;
use App\Service\TaskService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projects', name: 'projects_')]
class ProjectController extends BaseApiController
{
    use PaginatedApiFormatter;

    private ProjectRepositoryInterface $projectRepository;
    private ProjectService $projectService;
    private TaskService $taskService;
    private PaginatorInterface $paginator;
    private ResponseFormatterInterface $responseFormatter;

    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        ProjectService $projectService,
        TaskService $taskService,
        PaginatorInterface $paginator,
        ResponseFormatterInterface $responseFormatter
    ) {
        $this->projectRepository = $projectRepository;
        $this->projectService = $projectService;
        $this->taskService = $taskService;
        $this->paginator = $paginator;
        $this->responseFormatter = $responseFormatter;
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
        $project = $this->projectRepository->getActiveById($id);
        if (!$project) {
            return new NotFoundResponse();
        }

        return new SuccessResponse($project);
    }

    #[Route('/{id<\d+>}/tasks', name: 'tasks', methods: ['GET'])]
    public function tasks(Request $request, int $id): SuccessResponse
    {
        $project = $this->projectRepository->getActiveById($id);
        if (!$project) {
            return new NotFoundResponse();
        }
        $requestFilter = WebRequest::getRequestFilters($request);

        $tasks = $this->responseFormatter->formatListItems(
            $this->paginator->paginate(
                $this->taskService->getTasksByProject($id),
                $requestFilter->getPage(),
                $requestFilter->getLimitPerPage(),
            )
        );

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
