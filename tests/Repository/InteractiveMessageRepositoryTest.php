<?php

namespace LarkCustomBotBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use LarkCustomBotBundle\Entity\InteractiveMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Repository\InteractiveMessageRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(InteractiveMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class InteractiveMessageRepositoryTest extends AbstractRepositoryTestCase
{
    private InteractiveMessageRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(InteractiveMessageRepository::class);
    }

    public function testConstructShouldInitializeWithCorrectEntityClass(): void
    {
        $repository = self::getService(InteractiveMessageRepository::class);
        $this->assertInstanceOf(InteractiveMessageRepository::class, $repository);
    }

    public function testFindAllShouldReturnExistingEntitiesFromFixtures(): void
    {
        $result = $this->repository->findAll();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(InteractiveMessage::class, $result);
    }

    public function testFindByShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createInteractiveMessage($webhookUrl, ['title' => 'Existing Card']);
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['card' => ['title' => 'Non-existent Card']]);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindOneByWithNullField(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createInteractiveMessage($webhookUrl, ['title' => 'Test Card']);
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['card' => null]);

        $this->assertNull($result);
    }

    public function testFindOneByShouldReturnNullWhenNotFound(): void
    {
        $nonExistentWebhook = $this->createWebhookUrl();
        $nonExistentWebhook->setUrl('https://non-existent-webhook.com/hook');

        $result = $this->repository->findOneBy(['webhookUrl' => $nonExistentWebhook]);

        $this->assertNull($result);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);
        self::getEntityManager()->flush();

        $newCard = ['title' => 'New Card', 'content' => 'New content'];
        $message = $this->createInteractiveMessage($webhookUrl, $newCard);

        $this->repository->save($message);

        $found = $this->repository->findOneBy(['webhookUrl' => $webhookUrl]);
        $this->assertInstanceOf(InteractiveMessage::class, $found);
        $this->assertInstanceOf(WebhookUrl::class, $found->getWebhookUrl());
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);
        self::getEntityManager()->flush();

        $noFlushCard = ['title' => 'No Flush Card'];
        $message = $this->createInteractiveMessage($webhookUrl, $noFlushCard);

        $this->repository->save($message, false);

        $countBeforeFlush = $this->repository->count([]);

        self::getEntityManager()->flush();
        $countAfterFlush = $this->repository->count([]);
        $this->assertGreaterThan($countBeforeFlush, $countAfterFlush);
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $removeCard = ['title' => 'To Remove Card'];
        $message = $this->createInteractiveMessage($webhookUrl, $removeCard);
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $this->repository->remove($message);

        $found = $this->repository->findOneBy(['card' => $removeCard]);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $noFlushRemoveCard = ['title' => 'No Flush Remove Card'];
        $message = $this->createInteractiveMessage($webhookUrl, $noFlushRemoveCard);
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $this->repository->remove($message, false);

        $countBeforeFlush = $this->repository->count([]);

        self::getEntityManager()->flush();
        $countAfterFlush = $this->repository->count([]);
        $this->assertLessThan($countBeforeFlush, $countAfterFlush);
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

    /**
     * @param array<string, mixed> $card
     */
    private function createInteractiveMessage(WebhookUrl $webhookUrl, array $card): InteractiveMessage
    {
        $message = new InteractiveMessage();
        $message->setWebhookUrl($webhookUrl);
        $message->setCard($card);

        return $message;
    }

    public function testFindOneByWithOrderByClauseShouldReturnFirstOrderedEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message1 = $this->createInteractiveMessage($webhookUrl, ['title' => 'z-findone-order']);
        $message2 = $this->createInteractiveMessage($webhookUrl, ['title' => 'a-findone-order']);

        self::getEntityManager()->persist($message1);
        self::getEntityManager()->persist($message2);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['webhookUrl' => $webhookUrl], ['id' => 'ASC']);
        $this->assertInstanceOf(InteractiveMessage::class, $result);
        $this->assertSame($webhookUrl->getId(), $result->getWebhookUrl()->getId());
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

        $message1 = $this->createInteractiveMessage($webhookUrl1, ['title' => 'association-card-1']);
        $message2 = $this->createInteractiveMessage($webhookUrl1, ['title' => 'association-card-2']);
        $message3 = $this->createInteractiveMessage($webhookUrl2, ['title' => 'association-card-3']);

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

        $message = $this->createInteractiveMessage($webhookUrl, ['title' => 'association-test-card']);
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['webhookUrl' => $webhookUrl]);
        $this->assertInstanceOf(InteractiveMessage::class, $result);
        $this->assertSame($webhookUrl->getId(), $result->getWebhookUrl()->getId());
    }

    public function testSaveMethodShouldPersistEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);
        self::getEntityManager()->flush();

        $message = $this->createInteractiveMessage($webhookUrl, ['title' => 'save-method-test']);

        $this->repository->save($message, true);

        $found = $this->repository->find($message->getId());
        $this->assertInstanceOf(InteractiveMessage::class, $found);
        $this->assertSame($webhookUrl->getId(), $found->getWebhookUrl()->getId());
    }

    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createInteractiveMessage($webhookUrl, ['title' => 'remove-method-test']);
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $id = $message->getId();

        $this->repository->remove($message, true);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    /**
     * 创建一个新的交互消息实体用于测试
     */
    protected function createNewEntity(): object
    {
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName('Test Webhook ' . uniqid());
        $webhookUrl->setUrl('https://example.com/webhook');
        $webhookUrl->setValid(true);

        $message = new InteractiveMessage();
        $message->setWebhookUrl($webhookUrl);
        $message->setCard(['title' => 'Test Card ' . uniqid(), 'type' => 'test']);

        return $message;
    }

    /**
     * @return ServiceEntityRepository<InteractiveMessage>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
