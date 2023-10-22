<?php

namespace App\EventSubscriber;

use App\Client\NotificationInterface;
use App\Entity\Task;
use App\Event\TaskStateChangedEvent;
use App\Repository\TaskRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TaskStateChangeSubscriber implements EventSubscriberInterface
{
    private TaskRepository $taskRepository;
    private LoggerInterface $logger;
    private NotificationInterface $notifier;

    public function __construct(
        TaskRepository $taskRepository,
        LoggerInterface $logger,
        NotificationInterface $notifier
    ) {
        $this->taskRepository = $taskRepository;
        $this->logger = $logger;
        $this->notifier = $notifier;
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
        $this->logAndNotify($task);
    }

    private function handleProjectStateChangeEvent(Task $task): void
    {
        $project = $task->getProject();
        $project->updateProjectDuration();
        $project->updateProjectStatus();
    }

    private function logAndNotify(Task $task)
    {
        $this->logger->info(sprintf('Task #%d state changed ..', $task->getId()));
        $this->notifier->notify($task->jsonSerialize());
    }
}
