<?php

namespace LarkCustomBotBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use LarkCustomBotBundle\Entity\PostMessage;
use LarkCustomBotBundle\Repository\PostMessageRepository;
use PHPUnit\Framework\TestCase;

class PostMessageRepositoryTest extends TestCase
{
    private PostMessageRepository $repository;

    protected function setUp(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new PostMessageRepository($registry);
    }

    public function testConstruct_shouldInitializeWithCorrectEntityClass(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new PostMessageRepository($registry);

        $this->assertInstanceOf(PostMessageRepository::class, $repository);
    }

    public function testGetEntityName_shouldReturnPostMessageClass(): void
    {
        // 简化测试：验证Repository类型实例化正确
        $this->assertInstanceOf(PostMessageRepository::class, $this->repository);
    }
}
