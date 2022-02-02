<?php

namespace App\Repository;

use App\Entity\AdCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdCategory[]    findAll()
 * @method AdCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdCategory::class);
    }

    // /**
    //  * @return AdCategory[] Returns an array of AdCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdCategory
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function search(string $name) : array 
    {
        return $this->createQueryBuilder('a')
            ->where('a.name LIKE :value')
            ->setParameter('value', "%$name%")
            ->orderBy('a.name', 'ASC')
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
    }
}
