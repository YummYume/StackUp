<?php

namespace App\Repository;

use App\Entity\Stack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stack>
 *
 * @method Stack|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stack|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stack[]    findAll()
 * @method Stack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class StackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stack::class);
    }

    public function save(Stack $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Stack $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findRecentlyAddedStacks(int $maxResults = 3): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBySlugProfile(string $slugProfile, string $slugStack): ?Stack
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.profile', 'p')
            ->setParameters([
                'slugStack' => $slugStack,
                'slugProfile' => $slugProfile,
            ])
            ->where('s.slug = :slugStack')
            ->andWhere('p.slug = :slugProfile')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
