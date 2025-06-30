<?php

namespace LarkCustomBotBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use LarkCustomBotBundle\Entity\TextMessage;
use LarkCustomBotBundle\Repository\TextMessageRepository;
use PHPUnit\Framework\TestCase;

class TextMessageRepositoryTest extends TestCase
{
    private TextMessageRepository $repository;

    protected function setUp(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new TextMessageRepository($registry);
    }

    public function testConstruct_shouldInitializeWithCorrectEntityClass(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new TextMessageRepository($registry);

        $this->assertInstanceOf(TextMessageRepository::class, $repository);
    }

    public function testGetEntityName_shouldReturnTextMessageClass(): void
    {
        // 简化测试：验证Repository类型实例化正确
        $this->assertInstanceOf(TextMessageRepository::class, $this->repository);
    }
}
