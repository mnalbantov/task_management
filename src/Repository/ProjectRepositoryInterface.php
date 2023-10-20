<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Task;

interface ProjectRepositoryInterface
{
    public function findByTask(Task $task);

    public function save(Project $project);
}