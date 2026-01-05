<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }


    public function getRapTail(\DateTime $datDebut = null, \DateTime $datFin = null)
    {
    $qb = $this->createQueryBuilder('c')
    ->select('u.firstName', 'u.lastName', 'COUNT(c.id) as nombre', 'u.id as tailleur')
    ->innerJoin('c.usert', 'u')
    ->groupBy('tailleur');
    
    
    if ($datDebut) {
    $qb->andWhere('c.datCom >= :datDebut')
    ->setParameter('datDebut', $datDebut);
    }
    if ($datFin) {
    $qb->andWhere('c.datCom <= :datFin')
    ->setParameter('datFin', $datFin);
    }
        return $qb
        ->getQuery()
        ->getResult();
    }
   
    public function getRapConc(\DateTime $datDebut = null, \DateTime $datFin = null,$usert)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')   
            ;
            if ($datDebut) {
                $qb->andWhere('c.datCom >= :datDebut')
                ->setParameter('datDebut', $datDebut);
                }
                if ($datFin) {
                $qb->andWhere('c.datCom <= :datFin')
                ->setParameter('datFin', $datFin);
                }
        if ($usert) {
            $qb->andwhere('c.usert = :usert ');
            $qb->setParameter('usert',$usert);
    }
        return $qb
        ->getQuery()
        ->getResult();
    }
    public function getRapPeriode($datDebut,$datFin,$statut)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')   
            ;
            if ($datDebut) {
                $qb->where('c.datCom >= :datDebut');
                $qb->setParameter('datDebut', $datDebut);
            }
            if ($datFin) {
                $qb->andwhere('c.datCom <= :datFin');
                $qb->setParameter('datFin',$datFin);
        }
        if ($statut) {
            $qb->andwhere('c.statut = :stat ');
            $qb->setParameter('stat',$statut);
    }
        return $qb
        ->getQuery()
        ->getResult();
    }
    public function getCommandeAllStatut($statut)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('count(c.id) as nombre')
            ->where('c.statut= :statut')
            ->setParameter('statut',$statut)
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }
    
    public function getCommandeMois($statut,$debut,$fin)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('count(c.id) as nombre')           
            ->Where('c.statut = :statut and c.datCom BETWEEN :debut AND :fin')
            ->setParameter('statut', $statut)
            ->setParameter('debut', new \Datetime(date('Y')."-$debut"))
            ->setParameter('fin', new \Datetime(date('Y')."-$fin")) 
            
            ;
        return $qb
        ->getQuery()
        ->getResult();
    }

    public function getCommandeMoisTailleur($statut, $debut, $fin, $userId)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('count(c.id) as nombre')
            ->where('c.statut = :statut')
            ->andWhere('c.usert = :userId')
            ->setParameter('statut', $statut)
            ->setParameter('userId', $userId);
        return $qb->getQuery()->getResult();
    }
    //    /**
    //     * @return Commande[] Returns an array of Commande objects
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

    //    public function findOneBySomeField($value): ?Commande
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
