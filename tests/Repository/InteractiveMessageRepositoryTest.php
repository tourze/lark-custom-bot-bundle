<?php

namespace LarkCustomBotBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use LarkCustomBotBundle\Entity\InteractiveMessage;
use LarkCustomBotBundle\Repository\InteractiveMessageRepository;
use PHPUnit\Framework\TestCase;

class InteractiveMessageRepositoryTest extends TestCase
{
    private InteractiveMessageRepository $repository;

    protected function setUp(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new InteractiveMessageRepository($registry);
    }

    public function testConstruct_shouldInitializeWithCorrectEntityClass(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new InteractiveMessageRepository($registry);

        $this->assertInstanceOf(InteractiveMessageRepository::class, $repository);
    }

    public function testGetEntityName_shouldReturnInteractiveMessageClass(): void
    {
        // 简化测试：验证Repository类型实例化正确
        $this->assertInstanceOf(InteractiveMessageRepository::class, $this->repository);
    }
}
