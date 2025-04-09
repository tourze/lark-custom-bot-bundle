<?php

namespace LarkCustomBotBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LarkCustomBotBundle\Entity\InteractiveMessage;

/**
 * @extends ServiceEntityRepository<InteractiveMessage>
 *
 * @method InteractiveMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method InteractiveMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method InteractiveMessage[] findAll()
 * @method InteractiveMessage[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InteractiveMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InteractiveMessage::class);
    }
} 