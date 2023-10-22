<?php

namespace App\Repository;

use _PHPStan_d5c599c96\Nette\Utils\DateTime;
use App\Entity\Project;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

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

    public function getActive(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb
            ->select('p')
            ->from(Project::class, 'p')
            ->where('p.deletedAt IS NULL')
            ->getQuery()->getResult();
    }

    public function getActiveById(int $id): ?Project
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb
            ->select('p')
            ->from(Project::class, 'p')
            ->where('p.id = :id')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
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

    public function softDeleteWithAssociatedTasks(Project $project): void
    {
        //showcase transactional approach with direct QB for better performance
        $entityManager = $this->getEntityManager();
        $entityManager->wrapInTransaction(
            function () use ($project, $entityManager) {
                $dateTime = new DateTime();
                $project->setDeletedAt($dateTime);
                $entityManager->persist($project);

                $qb = $entityManager->createQueryBuilder();
                $qb
                    ->update(Task::class, 't')
                    ->set('t.deletedAt', ':deletedAt')
                    ->where('t.project = :projectId')
                    ->setParameter('deletedAt', $dateTime)
                    ->setParameter('projectId', $project->getId())
                    ->getQuery()
                    ->execute();
            }
        );
    }

    public function regularSoftDelete(Project $project): void
    {
        // showcase for associated soft delete for small number of items
        $project->setDeletedAt(new \DateTime());
        // Set deletedAt for associated tasks
        foreach ($project->getTasks() as $task) {
            $task->setDeletedAt(new \DateTime());
            $this->getEntityManager()->persist($task);
        }
        $this->getEntityManager()->flush();
    }
}
