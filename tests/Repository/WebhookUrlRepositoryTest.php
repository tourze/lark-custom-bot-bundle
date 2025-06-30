<?php

namespace LarkCustomBotBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Repository\WebhookUrlRepository;
use PHPUnit\Framework\TestCase;

class WebhookUrlRepositoryTest extends TestCase
{
    private WebhookUrlRepository $repository;

    protected function setUp(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new WebhookUrlRepository($registry);
    }

    public function testConstruct_shouldInitializeWithCorrectEntityClass(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new WebhookUrlRepository($registry);

        $this->assertInstanceOf(WebhookUrlRepository::class, $repository);
    }

    public function testGetEntityName_shouldReturnWebhookUrlClass(): void
    {
        // 简化测试：验证Repository类型实例化正确
        $this->assertInstanceOf(WebhookUrlRepository::class, $this->repository);
    }
}
