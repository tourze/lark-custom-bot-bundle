<?php

namespace LarkCustomBotBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LarkCustomBotBundle\Entity\PostMessage;

/**
 * @extends ServiceEntityRepository<PostMessage>
 *
 * @method PostMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostMessage[] findAll()
 * @method PostMessage[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostMessage::class);
    }
} 