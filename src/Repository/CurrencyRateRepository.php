<?php

namespace App\Repository;

use App\Entity\CurrencyRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CurrencyRate>
 */
class CurrencyRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyRate::class);
    }

//    /**
//     * @return CurrencyRate[] Returns an array of CurrencyRate objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CurrencyRate
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findOneBySomeFields($base, $currentDate, $currencyCode): ?CurrencyRate
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.base = :base')
            ->andWhere('c.datetime = :currentDate')
            ->andWhere('c.currencyCodeId = :currencyCode')
            ->setParameter('base', $base)
            ->setParameter('currentDate', $currentDate)
            ->setParameter('currencyCode', $currencyCode)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function deleteBySomeFields($base, $currentDate, $currencyCode)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->delete()
            ->andWhere('c.base = :base')
            ->andWhere('c.datetime = :currentDate')
            ->andWhere('c.currencyCodeId = :currencyCode')
            ->setParameter('base', $base)
            ->setParameter('currentDate', $currentDate)
            ->setParameter('currencyCode', $currencyCode);

        $query = $qb->getQuery();
        $query->execute();
    }
}
