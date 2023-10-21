<?php

namespace App\Service;

use App\Dto\ProjectRequest;
use App\Entity\Project;
use App\Event\ProjectCreatedEvent;
use App\Repository\ProjectRepository;
use App\Repository\ProjectRepositoryInterface;
use App\Request\WebRequest;
use App\Utils\Constants;
use Psr\EventDispatcher\EventDispatcherInterface;

class ProjectService
{
    private EventDispatcherInterface $eventDispatcher;
    private ProjectRepositoryInterface $projectRepository;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ProjectRepository $projectRepository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->projectRepository = $projectRepository;
    }

    public function getProjects(WebRequest $webRequest): array
    {
        $perPage = $webRequest->getLimitPerPage();
        $page = $webRequest->getPage();

        return $this->projectRepository->getProjects($page, $perPage);
    }

    public function createProject(ProjectRequest $projectRequest): Project
    {
        $project = new Project();
        $project->setTitle($projectRequest->getTitle());
        $project->setDescription($projectRequest->getDescription());
        $project->setStatus(Project::NEW);
        $project->setUserType($projectRequest->getUserType());

        $this->eventDispatcher->dispatch(
            new ProjectCreatedEvent($project),
            Constants::PROJECT_CREATED
        );

        return $project;
    }
}