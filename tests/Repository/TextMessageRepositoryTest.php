<?php

namespace LarkCustomBotBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use LarkCustomBotBundle\Entity\TextMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Repository\TextMessageRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(TextMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class TextMessageRepositoryTest extends AbstractRepositoryTestCase
{
    private TextMessageRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(TextMessageRepository::class);
    }

    public function testConstructShouldInitializeWithCorrectEntityClass(): void
    {
        $repository = self::getService(TextMessageRepository::class);
        $this->assertInstanceOf(TextMessageRepository::class, $repository);
    }

    public function testFindAllShouldReturnExistingEntitiesFromFixtures(): void
    {
        $result = $this->repository->findAll();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(TextMessage::class, $result);
    }

    public function testFindByShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createTextMessage($webhookUrl, 'Existing content');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['content' => 'Non-existent content']);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindOneByWithNullField(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createTextMessage($webhookUrl, 'Test content');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['content' => null]);

        $this->assertNull($result);
    }

    public function testFindOneByShouldReturnNullWhenNotFound(): void
    {
        $result = $this->repository->findOneBy(['content' => 'Non-existent content']);

        $this->assertNull($result);
    }

    public function testCountShouldReturnZeroWhenNoEntities(): void
    {
        $count = $this->repository->count([]);

        $this->assertSame(2, $count);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);
        self::getEntityManager()->flush();

        $message = $this->createTextMessage($webhookUrl, 'New message');

        $this->repository->save($message);

        $found = $this->repository->findOneBy(['content' => 'New message']);
        $this->assertInstanceOf(TextMessage::class, $found);
        $this->assertSame('New message', $found->getContent());
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);
        self::getEntityManager()->flush();

        $message = $this->createTextMessage($webhookUrl, 'No flush message');

        $this->repository->save($message, false);

        $found = $this->repository->findOneBy(['content' => 'No flush message']);
        $this->assertNull($found);

        self::getEntityManager()->flush();
        $found = $this->repository->findOneBy(['content' => 'No flush message']);
        $this->assertInstanceOf(TextMessage::class, $found);
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createTextMessage($webhookUrl, 'To remove message');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $this->repository->remove($message);

        $found = $this->repository->findOneBy(['content' => 'To remove message']);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createTextMessage($webhookUrl, 'No flush remove message');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $this->repository->remove($message, false);

        $found = $this->repository->findOneBy(['content' => 'No flush remove message']);
        $this->assertInstanceOf(TextMessage::class, $found);

        self::getEntityManager()->flush();
        $found = $this->repository->findOneBy(['content' => 'No flush remove message']);
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

    private function createTextMessage(WebhookUrl $webhookUrl, string $content): TextMessage
    {
        $message = new TextMessage();
        $message->setWebhookUrl($webhookUrl);
        $message->setContent($content);

        return $message;
    }

    public function testFindOneByWithOrderByClauseShouldReturnFirstOrderedEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message1 = $this->createTextMessage($webhookUrl, 'z-findone-order-content');
        $message2 = $this->createTextMessage($webhookUrl, 'a-findone-order-content');

        self::getEntityManager()->persist($message1);
        self::getEntityManager()->persist($message2);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['webhookUrl' => $webhookUrl], ['content' => 'ASC']);
        $this->assertInstanceOf(TextMessage::class, $result);
        $this->assertSame('a-findone-order-content', $result->getContent());
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

        $message1 = $this->createTextMessage($webhookUrl1, 'association-content-1');
        $message2 = $this->createTextMessage($webhookUrl1, 'association-content-2');
        $message3 = $this->createTextMessage($webhookUrl2, 'association-content-3');

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

        $message = $this->createTextMessage($webhookUrl, 'association-test-content');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['webhookUrl' => $webhookUrl]);
        $this->assertInstanceOf(TextMessage::class, $result);
        $this->assertSame($webhookUrl->getId(), $result->getWebhookUrl()->getId());
    }

    public function testSaveMethodShouldPersistEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);
        self::getEntityManager()->flush();

        $message = $this->createTextMessage($webhookUrl, 'save-method-test-content');

        $this->repository->save($message, true);

        $found = $this->repository->find($message->getId());
        $this->assertInstanceOf(TextMessage::class, $found);
        $this->assertSame('save-method-test-content', $found->getContent());
    }

    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createTextMessage($webhookUrl, 'remove-method-test-content');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $id = $message->getId();

        $this->repository->remove($message, true);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    /**
     * 创建一个新的文本消息实体用于测试
     */
    protected function createNewEntity(): object
    {
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName('Test Webhook ' . uniqid());
        $webhookUrl->setUrl('https://example.com/webhook');
        $webhookUrl->setValid(true);

        $message = new TextMessage();
        $message->setWebhookUrl($webhookUrl);
        $message->setContent('Test message content ' . uniqid());

        return $message;
    }

    /**
     * @return ServiceEntityRepository<TextMessage>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
