<?php

namespace LarkCustomBotBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LarkCustomBotBundle\Entity\ShareChatMessage;

/**
 * @extends ServiceEntityRepository<ShareChatMessage>
 *
 * @method ShareChatMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShareChatMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShareChatMessage[] findAll()
 * @method ShareChatMessage[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShareChatMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShareChatMessage::class);
    }
} 