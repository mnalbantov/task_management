<?php

namespace App\Event;

use App\Entity\Project;
use Symfony\Contracts\EventDispatcher\Event;

class ProjectCreatedEvent extends Event
{
    private Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
