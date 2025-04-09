<?php

namespace LarkCustomBotBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LarkCustomBotBundle\Entity\TextMessage;

/**
 * @extends ServiceEntityRepository<TextMessage>
 *
 * @method TextMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TextMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TextMessage[] findAll()
 * @method TextMessage[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TextMessage::class);
    }
}
