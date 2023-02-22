<?php

namespace App\Repository;

use App\Entity\TechPicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TechPicture>
 *
 * @method TechPicture|null find($id, $lockMode = null, $lockVersion = null)
 * @method TechPicture|null findOneBy(array $criteria, array $orderBy = null)
 * @method TechPicture[]    findAll()
 * @method TechPicture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class TechPictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TechPicture::class);
    }

    public function save(TechPicture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TechPicture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
