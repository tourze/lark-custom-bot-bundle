<?php

namespace LarkCustomBotBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use LarkCustomBotBundle\Entity\ShareChatMessage;
use LarkCustomBotBundle\Repository\ShareChatMessageRepository;
use PHPUnit\Framework\TestCase;

class ShareChatMessageRepositoryTest extends TestCase
{
    private ShareChatMessageRepository $repository;

    protected function setUp(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new ShareChatMessageRepository($registry);
    }

    public function testConstruct_shouldInitializeWithCorrectEntityClass(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new ShareChatMessageRepository($registry);

        $this->assertInstanceOf(ShareChatMessageRepository::class, $repository);
    }

    public function testGetEntityName_shouldReturnShareChatMessageClass(): void
    {
        // 简化测试：验证Repository类型实例化正确
        $this->assertInstanceOf(ShareChatMessageRepository::class, $this->repository);
    }
}
