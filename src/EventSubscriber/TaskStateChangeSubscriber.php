<?php

namespace App\EventSubscriber;

use App\Event\TaskStateChangedEvent;
use App\Repository\TaskRepository;
use App\Utils\Helper;
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
        return [
            TaskStateChangedEvent::class => 'onTaskStateChanged',
        ];
    }

    public function onTaskStateChanged(TaskStateChangedEvent $event)
    {
        $task = $event->getTask();

        $project = $task->getProject();
        $project->updateProjectStatus();

        if ($project->getStatus() === Helper::PROJECT_DONE) {
            // Check if the project is delayed and mark it as "failed" if necessary
//            if ($project->isProjectDelayed()) {
//                $project->setStatus(Helper::PROJECT_FAILED);
//            }
        }
        $this->taskRepository->save($task);
    }
}