<?php

namespace App\Repository;

use App\Entity\NewsHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NewsHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewsHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewsHistory[]    findAll()
 * @method NewsHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsHistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NewsHistory::class);
    }

    // /**
    //  * @return NewsHistory[] Returns an array of NewsHistory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NewsHistory
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
