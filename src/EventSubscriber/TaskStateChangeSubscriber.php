<?php

namespace App\EventSubscriber;

use App\Entity\Task;
use App\Event\TaskStateChangedEvent;
use App\Repository\TaskRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TaskStateChangeSubscriber implements EventSubscriberInterface
{

    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public static function getSubscribedEvents(): array
    {
        // use subscribers for state changes
        // can be extended as per our design & goals
        return [
            TaskStateChangedEvent::class => 'onTaskStateChanged',
        ];
    }

    public function onTaskStateChanged(TaskStateChangedEvent $event): void
    {
        $task = $event->getTask();
        $this->handleProjectStateChangeEvent($task);
        $this->taskRepository->save($task);
    }

    private function handleProjectStateChangeEvent(Task $task): void
    {
        $project = $task->getProject();
        $project->updateProjectDuration();
        $project->updateProjectStatus();
    }
}