<?php

namespace App\Repository;

use App\Entity\Category;
use App\Enum\RequestStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findTrendingCategories(\DateTime $since, \DateTime $before = new \DateTime(), int $maxResults = 3): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->leftJoin('c.techs', 't')
            ->leftJoin('t.request', 'r')
            ->select('COUNT(t) AS HIDDEN techCount', 'c')
            ->where($qb->expr()->between('c.createdAt', ':since', ':before'))
            ->andWhere($qb->expr()->eq('r.created', true))
            ->andWhere($qb->expr()->not($qb->expr()->eq('r.status', ':rejected')))
            ->setParameters([
                'since' => $since,
                'before' => $before,
                'rejected' => RequestStatusEnum::Rejected,
            ])
            ->setMaxResults($maxResults)
            ->orderBy('techCount', 'DESC')
            ->addOrderBy('t.createdAt', 'DESC')
            ->groupBy('c')
            ->getQuery()
            ->getResult()
        ;
    }
}
