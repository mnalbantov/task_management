<?php

namespace App\Controller\Web;

use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Request\WebRequest;
use App\Service\TaskService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projects', name: 'web_projects_')]
class HomeController extends AbstractController
{

    private ProjectRepository $projectRepository;
    private PaginatorInterface $paginator;
    private TaskRepository $taskRepository;
    private TaskService $taskService;

    public function __construct(
        ProjectRepository $projectRepository,
        TaskService $taskService,
        PaginatorInterface $paginator,

    ) {
        $this->projectRepository = $projectRepository;
        $this->paginator = $paginator;
        $this->taskService = $taskService;
    }

    #[Route('/', name: 'list', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $requestFilter = WebRequest::getRequestFilters($request);
        // Demo for using Knp Paginator
        $projects = $this->paginator->paginate(
            $this->projectRepository->findAll(),
            $requestFilter->getPage(),
            $requestFilter->getLimitPerPage()
        );

        return $this->render('project/list.html.twig', ['projects' => $projects]);
    }

    #[Route('/{id<\d+>}/tasks', name: 'tasks', methods: ['GET'])]
    public function tasks(Request $request, int $id): Response
    {
        $requestFilter = WebRequest::getRequestFilters($request);

        $tasks = $this->paginator->paginate(
            $this->taskService->getTasksByProject($id),
            $requestFilter->getPage(),
            $requestFilter->getLimitPerPage()
        );

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }
}