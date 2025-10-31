<?php

namespace LarkCustomBotBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use LarkCustomBotBundle\Entity\ShareChatMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Repository\ShareChatMessageRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ShareChatMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class ShareChatMessageRepositoryTest extends AbstractRepositoryTestCase
{
    private ShareChatMessageRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ShareChatMessageRepository::class);
    }

    public function testConstructShouldInitializeWithCorrectEntityClass(): void
    {
        $repository = self::getService(ShareChatMessageRepository::class);
        $this->assertInstanceOf(ShareChatMessageRepository::class, $repository);
    }

    public function testFindAllShouldReturnExistingEntitiesFromFixtures(): void
    {
        $result = $this->repository->findAll();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(ShareChatMessage::class, $result);
    }

    public function testFindByShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createShareChatMessage($webhookUrl, 'existing-chat-id');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['chatId' => 'non-existent-chat-id']);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindOneByWithNullField(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createShareChatMessage($webhookUrl, 'test-chat-id');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['chatId' => null]);

        $this->assertNull($result);
    }

    public function testFindOneByShouldReturnNullWhenNotFound(): void
    {
        $result = $this->repository->findOneBy(['chatId' => 'non-existent-chat-id']);

        $this->assertNull($result);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);
        self::getEntityManager()->flush();

        $message = $this->createShareChatMessage($webhookUrl, 'new-chat-id');

        $this->repository->save($message);

        $found = $this->repository->findOneBy(['chatId' => 'new-chat-id']);
        $this->assertInstanceOf(ShareChatMessage::class, $found);
        $this->assertSame('new-chat-id', $found->getChatId());
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);
        self::getEntityManager()->flush();

        $message = $this->createShareChatMessage($webhookUrl, 'no-flush-chat-id');

        $this->repository->save($message, false);

        $found = $this->repository->findOneBy(['chatId' => 'no-flush-chat-id']);
        $this->assertNull($found);

        self::getEntityManager()->flush();
        $found = $this->repository->findOneBy(['chatId' => 'no-flush-chat-id']);
        $this->assertInstanceOf(ShareChatMessage::class, $found);
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createShareChatMessage($webhookUrl, 'to-remove-chat-id');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $this->repository->remove($message);

        $found = $this->repository->findOneBy(['chatId' => 'to-remove-chat-id']);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createShareChatMessage($webhookUrl, 'no-flush-remove-chat-id');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $this->repository->remove($message, false);

        $found = $this->repository->findOneBy(['chatId' => 'no-flush-remove-chat-id']);
        $this->assertInstanceOf(ShareChatMessage::class, $found);

        self::getEntityManager()->flush();
        $found = $this->repository->findOneBy(['chatId' => 'no-flush-remove-chat-id']);
        $this->assertNull($found);
    }

    public function testFindByWithInvalidFieldShouldThrowException(): void
    {
        $this->expectException(\Exception::class);
        $this->repository->findBy(['nonExistentField' => 'value']);
    }

    private function createWebhookUrl(): WebhookUrl
    {
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName('Test Webhook');
        $webhookUrl->setUrl('https://example.com/webhook');
        $webhookUrl->setValid(true);

        return $webhookUrl;
    }

    private function createShareChatMessage(WebhookUrl $webhookUrl, string $chatId): ShareChatMessage
    {
        $message = new ShareChatMessage();
        $message->setWebhookUrl($webhookUrl);
        $message->setChatId($chatId);

        return $message;
    }

    public function testFindOneByWithOrderByClauseShouldReturnFirstOrderedEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message1 = $this->createShareChatMessage($webhookUrl, 'z-findone-order');
        $message2 = $this->createShareChatMessage($webhookUrl, 'a-findone-order');

        self::getEntityManager()->persist($message1);
        self::getEntityManager()->persist($message2);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['webhookUrl' => $webhookUrl], ['chatId' => 'ASC']);
        $this->assertInstanceOf(ShareChatMessage::class, $result);
        $this->assertSame('a-findone-order', $result->getChatId());
    }

    public function testCountByAssociationWebhookUrlShouldReturnCorrectNumber(): void
    {
        $webhookUrl1 = $this->createWebhookUrl();
        $webhookUrl1->setName('Webhook 1');
        $webhookUrl1->setUrl('https://example.com/webhook1');

        $webhookUrl2 = $this->createWebhookUrl();
        $webhookUrl2->setName('Webhook 2');
        $webhookUrl2->setUrl('https://example.com/webhook2');

        self::getEntityManager()->persist($webhookUrl1);
        self::getEntityManager()->persist($webhookUrl2);

        $message1 = $this->createShareChatMessage($webhookUrl1, 'association-chat-1');
        $message2 = $this->createShareChatMessage($webhookUrl1, 'association-chat-2');
        $message3 = $this->createShareChatMessage($webhookUrl2, 'association-chat-3');

        self::getEntityManager()->persist($message1);
        self::getEntityManager()->persist($message2);
        self::getEntityManager()->persist($message3);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['webhookUrl' => $webhookUrl1]);
        $this->assertSame(2, $count);
    }

    public function testFindOneByAssociationWebhookUrlShouldReturnMatchingEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        $webhookUrl->setName('Association Test Webhook');
        $webhookUrl->setUrl('https://example.com/association-test');

        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createShareChatMessage($webhookUrl, 'association-test-chat');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['webhookUrl' => $webhookUrl]);
        $this->assertInstanceOf(ShareChatMessage::class, $result);
        $this->assertSame($webhookUrl->getId(), $result->getWebhookUrl()->getId());
    }

    public function testSaveMethodShouldPersistEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);
        self::getEntityManager()->flush();

        $message = $this->createShareChatMessage($webhookUrl, 'save-method-test');

        $this->repository->save($message, true);

        $found = $this->repository->find($message->getId());
        $this->assertInstanceOf(ShareChatMessage::class, $found);
        $this->assertSame('save-method-test', $found->getChatId());
    }

    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createShareChatMessage($webhookUrl, 'remove-method-test');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $id = $message->getId();

        $this->repository->remove($message, true);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    /**
     * 创建一个新的分享聊天消息实体用于测试
     */
    protected function createNewEntity(): object
    {
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName('Test Webhook ' . uniqid());
        $webhookUrl->setUrl('https://example.com/webhook');
        $webhookUrl->setValid(true);

        $message = new ShareChatMessage();
        $message->setWebhookUrl($webhookUrl);
        $message->setChatId('test-chat-' . uniqid());

        return $message;
    }

    /**
     * @return ServiceEntityRepository<ShareChatMessage>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
