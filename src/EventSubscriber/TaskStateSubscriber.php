<?php

namespace App\EventSubscriber;

use App\Event\TaskStateChangedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TaskStateSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            TaskStateChangedEvent::class => 'onTaskStateChanged',
        ];
    }

    public function onTaskStateChanged(TaskStateChangedEvent $event)
    {

    }
}