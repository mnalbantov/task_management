<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    use PaginatorTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function persist(Task $task): void
    {
        $this->getEntityManager()->persist($task);
    }

    public function save(Task $task): void
    {
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
    }

    // demo for using custom paginator
    public function getPaginatedTasksByProjectId(int $projectId, int $page, int $perPage): array
    {
        $qb = $this->createQueryBuilder('t');

        $qb
            ->where('t.project = :id')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameter(':id', $projectId);

        return $this->usePaginatedResponse($qb, $page, $perPage);
    }

    public function getTasksByProjectId(int $projectId): array
    {
        $qb = $this->createQueryBuilder('t');

        $qb
            ->where('t.project = :id')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameter(':id', $projectId);

        return $qb->getQuery()->getResult();
    }

    public function getActiveTaskById(int $id): ?Task
    {
        $qb = $this->createQueryBuilder('t');

        return $qb
            ->where('t.id = :id')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
