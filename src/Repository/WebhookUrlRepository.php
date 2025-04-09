<?php

namespace LarkCustomBotBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LarkCustomBotBundle\Entity\WebhookUrl;

/**
 * @method WebhookUrl|null find($id, $lockMode = null, $lockVersion = null)
 * @method WebhookUrl|null findOneBy(array $criteria, array $orderBy = null)
 * @method WebhookUrl[] findAll()
 * @method WebhookUrl[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebhookUrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebhookUrl::class);
    }

    //    /**
    //     * @return FeishuRobotConfig[] Returns an array of FeishuRobotConfig objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FeishuRobotConfig
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
