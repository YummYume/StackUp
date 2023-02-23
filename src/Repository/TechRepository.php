<?php

namespace App\Repository;

use App\Entity\Tech;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tech>
 *
 * @method Tech|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tech|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tech[]    findAll()
 * @method Tech[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class TechRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tech::class);
    }

    public function save(Tech $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tech $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findCreatedTech(array $fields): array
    {
        $name = $fields['name'] ?? null;

        if (null === $name) {
            return [];
        }

        $qb = $this->createQueryBuilder('t');

        return $qb
            ->leftJoin('t.request', 'r')
            ->where($qb->expr()->eq('t.name', ':name'))
            ->andWhere($qb->expr()->eq('r.created', true))
            ->setParameter('name', trim($name))
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }
}
