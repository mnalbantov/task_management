<?php

namespace App\EventListener;

use App\Event\ProjectTaskCreatedEvent;
use App\Repository\TaskRepository;

class ProjectTaskCreatedListener
{
    private TaskRepository $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function onTaskCreated(ProjectTaskCreatedEvent $event): void
    {
        // the purpose of this listener is to demonstrate ability for different
        // actions like trigger other mechanism etc
        $task = $event->getTask();
        if (!$task->getStartDate()) {
            $task->setStartDate(new \DateTime());
        }
        $project = $task->getProject();
        $project->updateProjectDuration();
        $project->updateProjectStatus();
        $this->repository->save($task);
    }
}