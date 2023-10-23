<?php

namespace App\EventListener;

use App\Client\NotificationInterface;
use App\Entity\Task;
use App\Event\ProjectTaskCreatedEvent;
use App\Repository\TaskRepository;

class ProjectTaskCreatedListener
{
    private TaskRepository $repository;
    private NotificationInterface $notifier;

    public function __construct(TaskRepository $repository, NotificationInterface $notifier)
    {
        $this->repository = $repository;
        $this->notifier = $notifier;
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
        $this->notifyManagement($task);
    }

    private function notifyManagement(Task $task): void
    {
        $this->notifier->notify($task->jsonSerialize());
    }
}
