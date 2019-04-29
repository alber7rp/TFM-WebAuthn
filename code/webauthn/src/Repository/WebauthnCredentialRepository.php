<?php

namespace App\Repository;

use App\Entity\WebauthnCredential;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WebauthnCredential|null find($id, $lockMode = null, $lockVersion = null)
 * @method WebauthnCredential|null findOneBy(array $criteria, array $orderBy = null)
 * @method WebauthnCredential[]    findAll()
 * @method WebauthnCredential[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebauthnCredentialRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WebauthnCredential::class);
    }

    // /**
    //  * @return WebauthnCredential[] Returns an array of WebauthnCredential objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WebauthnCredential
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
