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

        // @fixme this could be optimised for querying not deleted project directly
        /** @var Project $project */
        $project = $this->projectRepository->find(
            $value
        );
        $forbiddenStatuses = [Project::FAILED, Project::DONE];

        if (null === $project) {
            $this->buildViolation($constraint, $value);
        }
        if ($project) {
            // deleted project
            if (null !== $project->getDeletedAt()) {
                $this->buildViolation($constraint, $value);
            }
            // project is completed or deadline passed
            $endDate = $project->getEndDate();
            if ((null !== $endDate && $endDate < new \DateTime()) || in_array(
                $project->getStatus(),
                $forbiddenStatuses
            )) {
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
