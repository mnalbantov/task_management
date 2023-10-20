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
        $task = $event->getTask();
        if (!$task->getStartDate()) {
            $task->setStartDate(new \DateTimeImmutable());
        }
        $this->repository->save($task);
    }
}