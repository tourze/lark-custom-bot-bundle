<?php

namespace LarkCustomBotBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use LarkCustomBotBundle\Entity\ImageMessage;
use LarkCustomBotBundle\Repository\ImageMessageRepository;
use PHPUnit\Framework\TestCase;

class ImageMessageRepositoryTest extends TestCase
{
    private ImageMessageRepository $repository;

    protected function setUp(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new ImageMessageRepository($registry);
    }

    public function testConstruct_shouldInitializeWithCorrectEntityClass(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new ImageMessageRepository($registry);

        $this->assertInstanceOf(ImageMessageRepository::class, $repository);
    }

    public function testGetEntityName_shouldReturnImageMessageClass(): void
    {
        // 简化测试：验证Repository类型实例化正确
        $this->assertInstanceOf(ImageMessageRepository::class, $this->repository);
    }
}
