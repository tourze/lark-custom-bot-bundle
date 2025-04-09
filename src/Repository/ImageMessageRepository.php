<?php

namespace LarkCustomBotBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LarkCustomBotBundle\Entity\ImageMessage;

/**
 * @extends ServiceEntityRepository<ImageMessage>
 *
 * @method ImageMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageMessage[] findAll()
 * @method ImageMessage[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageMessage::class);
    }
} 