<?php

namespace App\Repository;

use App\Entity\Tech;
use App\Entity\User;
use App\Enum\RequestStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function findRecentlyAddedTechs(int $maxResults = 3): array
    {
        $qb = $this->createQueryBuilder('t');

        return $qb
            ->leftJoin('t.request', 'r')
            ->where($qb->expr()->eq('r.created', true))
            ->andWhere($qb->expr()->not($qb->expr()->eq('r.status', ':rejected')))
            ->setParameter('rejected', RequestStatusEnum::Rejected)
            ->orderBy('r.submittedAt', 'DESC')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findTrendingTechs(\DateTime $since, \DateTime $before = new \DateTime(), int $maxResults = 3): array
    {
        $qb = $this->createQueryBuilder('t');

        return $qb
            ->leftJoin('t.request', 'r')
            ->leftJoin('r.votes', 'v')
            ->select('COUNT(v) AS HIDDEN voteCount', 't')
            ->where($qb->expr()->between('r.submittedAt', ':since', ':before'))
            ->andWhere($qb->expr()->eq('r.created', true))
            ->andWhere($qb->expr()->not($qb->expr()->eq('r.status', ':rejected')))
            ->setParameters([
                'since' => $since,
                'before' => $before,
                'rejected' => RequestStatusEnum::Rejected,
            ])
            ->setMaxResults($maxResults)
            ->orderBy('voteCount', 'DESC')
            ->addOrderBy('r.submittedAt', 'DESC')
            ->groupBy('t')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getPendingAndAcceptedTechs(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t');

        return $qb
            ->leftJoin('t.request', 'r')
            ->where($qb->expr()->eq('r.created', true))
            ->andWhere($qb->expr()->not($qb->expr()->eq('r.status', ':rejected')))
            ->setParameter('rejected', RequestStatusEnum::Rejected)
            ->orderBy('t.name', 'ASC')
        ;
    }

    public function getSearchableTechs(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t');

        return $qb
            ->leftJoin('t.request', 'r')
            ->where($qb->expr()->eq('r.created', true))
        ;
    }

    public function findSearchableTech(string $slug): ?Tech
    {
        $qb = $this->createQueryBuilder('t');

        return $qb
            ->leftJoin('t.request', 'r')
            ->where($qb->expr()->eq('r.created', true))
            ->andWhere($qb->expr()->eq('t.slug', ':slug'))
            ->setParameter('slug', $slug)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findUnreleasedTechForUser(User $user): ?Tech
    {
        $qb = $this->createQueryBuilder('t');

        return $qb
            ->leftJoin('t.request', 'r')
            ->where($qb->expr()->eq('r.created', 'false'))
            ->andWhere($qb->expr()->eq('t.createdBy', ':user'))
            ->setParameter('user', $user->getId()->toBinary())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
