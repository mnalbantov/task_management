<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository implements ProjectRepositoryInterface
{
    use PaginatorTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function getProjects(int $page, int $perPage): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('p')
            ->from(Project::class, 'p')
            ->where('1 = 1');

        return $this->usePaginatedResponse($qb, $page, $perPage);
    }

    public function save(Project $project)
    {
        $this->getEntityManager()->persist($project);
        $this->getEntityManager()->flush();
    }

    public function findByTask(Task $task)
    {
        // TODO: Implement findByTask() method.
    }
}
