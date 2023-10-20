<?php

namespace App\Validator;

use App\Entity\Project;
use App\Repository\ProjectRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProjectIsActiveValidator extends ConstraintValidator
{
    private ProjectRepositoryInterface $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        /**@var Project $project */
        $project = $this->projectRepository->find($value);
        $forbiddenStatuses = [Project::FAILED, Project::DONE];

        if (null === $project) {
            $this->buildViolation($constraint, $value);
        }
        if ($project) {
            // project is completed or deadline passed
            $endDate = $project->getEndDate();
            if (($endDate !== null && $endDate < new \DateTime()) || in_array($project, $forbiddenStatuses)) {
                $this->buildViolation($constraint, $project->getTitle());
            }
        }
    }

    private function buildViolation(Constraint $constraint, string $value)
    {
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
