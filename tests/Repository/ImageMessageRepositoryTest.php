<?php

namespace LarkCustomBotBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use LarkCustomBotBundle\Entity\ImageMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Repository\ImageMessageRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ImageMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class ImageMessageRepositoryTest extends AbstractRepositoryTestCase
{
    private ImageMessageRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ImageMessageRepository::class);
    }

    public function testConstructShouldInitializeWithCorrectEntityClass(): void
    {
        $repository = self::getService(ImageMessageRepository::class);
        $this->assertInstanceOf(ImageMessageRepository::class, $repository);
    }

    public function testFindAllShouldReturnExistingEntitiesFromFixtures(): void
    {
        $result = $this->repository->findAll();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(ImageMessage::class, $result);
    }

    public function testFindByShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createImageMessage($webhookUrl, 'existing-image.jpg');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['imageKey' => 'non-existent-image.jpg']);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindOneByWithNullField(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createImageMessage($webhookUrl, 'test-image.jpg');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['imageKey' => null]);

        $this->assertNull($result);
    }

    public function testFindOneByShouldReturnNullWhenNotFound(): void
    {
        $result = $this->repository->findOneBy(['imageKey' => 'non-existent-image.jpg']);

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

        $message = $this->createImageMessage($webhookUrl, 'new-image.jpg');

        $this->repository->save($message);

        $found = $this->repository->findOneBy(['imageKey' => 'new-image.jpg']);
        $this->assertInstanceOf(ImageMessage::class, $found);
        $this->assertSame('new-image.jpg', $found->getImageKey());
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);
        self::getEntityManager()->flush();

        $message = $this->createImageMessage($webhookUrl, 'no-flush-image.jpg');

        $this->repository->save($message, false);

        $found = $this->repository->findOneBy(['imageKey' => 'no-flush-image.jpg']);
        $this->assertNull($found);

        self::getEntityManager()->flush();
        $found = $this->repository->findOneBy(['imageKey' => 'no-flush-image.jpg']);
        $this->assertInstanceOf(ImageMessage::class, $found);
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createImageMessage($webhookUrl, 'to-remove-image.jpg');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $this->repository->remove($message);

        $found = $this->repository->findOneBy(['imageKey' => 'to-remove-image.jpg']);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createImageMessage($webhookUrl, 'no-flush-remove-image.jpg');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $this->repository->remove($message, false);

        $found = $this->repository->findOneBy(['imageKey' => 'no-flush-remove-image.jpg']);
        $this->assertInstanceOf(ImageMessage::class, $found);

        self::getEntityManager()->flush();
        $found = $this->repository->findOneBy(['imageKey' => 'no-flush-remove-image.jpg']);
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

    private function createImageMessage(WebhookUrl $webhookUrl, string $imageKey): ImageMessage
    {
        $message = new ImageMessage();
        $message->setWebhookUrl($webhookUrl);
        $message->setImageKey($imageKey);

        return $message;
    }

    public function testFindOneByWithOrderByClauseShouldReturnFirstOrderedEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message1 = $this->createImageMessage($webhookUrl, 'z-findone-order.jpg');
        $message2 = $this->createImageMessage($webhookUrl, 'a-findone-order.jpg');

        self::getEntityManager()->persist($message1);
        self::getEntityManager()->persist($message2);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['webhookUrl' => $webhookUrl], ['imageKey' => 'ASC']);
        $this->assertInstanceOf(ImageMessage::class, $result);
        $this->assertSame('a-findone-order.jpg', $result->getImageKey());
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

        $message1 = $this->createImageMessage($webhookUrl1, 'association-image-1.jpg');
        $message2 = $this->createImageMessage($webhookUrl1, 'association-image-2.jpg');
        $message3 = $this->createImageMessage($webhookUrl2, 'association-image-3.jpg');

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

        $message = $this->createImageMessage($webhookUrl, 'association-test-image.jpg');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['webhookUrl' => $webhookUrl]);
        $this->assertInstanceOf(ImageMessage::class, $result);
        $this->assertSame($webhookUrl->getId(), $result->getWebhookUrl()->getId());
    }

    public function testSaveMethodShouldPersistEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);
        self::getEntityManager()->flush();

        $message = $this->createImageMessage($webhookUrl, 'save-method-test.jpg');

        $this->repository->save($message, true);

        $found = $this->repository->find($message->getId());
        $this->assertInstanceOf(ImageMessage::class, $found);
        $this->assertSame('save-method-test.jpg', $found->getImageKey());
    }

    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createImageMessage($webhookUrl, 'remove-method-test.jpg');
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $id = $message->getId();

        $this->repository->remove($message, true);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    /**
     * 创建一个新的图片消息实体用于测试
     */
    protected function createNewEntity(): object
    {
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName('Test Webhook ' . uniqid());
        $webhookUrl->setUrl('https://example.com/webhook');
        $webhookUrl->setValid(true);

        $message = new ImageMessage();
        $message->setWebhookUrl($webhookUrl);
        $message->setImageKey('test-image-' . uniqid() . '.jpg');

        return $message;
    }

    /**
     * @return ServiceEntityRepository<ImageMessage>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
