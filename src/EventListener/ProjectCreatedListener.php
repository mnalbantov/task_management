<?php

namespace App\EventListener;

use App\Entity\Project;
use App\Event\ProjectCreatedEvent;
use App\Repository\ProjectRepository;
use App\Repository\ProjectRepositoryInterface;

class ProjectCreatedListener
{
    private ProjectRepository $repository;

    public function __construct(ProjectRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function onProjectCreated(ProjectCreatedEvent $event): void
    {
        $project = $event->getProject();

        if (!$project->getStartDate()) {
            $project->setStartDate(new \DateTimeImmutable());
        }
        $project->setStatus(Project::NEW); // new project as always new status
        $this->repository->save($project);
    }
}