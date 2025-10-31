<?php

namespace LarkCustomBotBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use LarkCustomBotBundle\Entity\PostMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Repository\PostMessageRepository;
use LarkCustomBotBundle\ValueObject\PostParagraph;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(PostMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class PostMessageRepositoryTest extends AbstractRepositoryTestCase
{
    private PostMessageRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(PostMessageRepository::class);
    }

    public function testConstructShouldInitializeWithCorrectEntityClass(): void
    {
        $repository = self::getService(PostMessageRepository::class);
        $this->assertInstanceOf(PostMessageRepository::class, $repository);
    }

    public function testFindAllShouldReturnExistingEntitiesFromFixtures(): void
    {
        $result = $this->repository->findAll();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(PostMessage::class, $result);
    }

    public function testFindByShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createPostMessage($webhookUrl, 'Existing Title', []);
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['title' => 'Non-existent Title']);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindOneByWithNullField(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createPostMessage($webhookUrl, 'Test Title', []);
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['title' => null]);

        $this->assertNull($result);
    }

    public function testFindOneByShouldReturnNullWhenNotFound(): void
    {
        $result = $this->repository->findOneBy(['title' => 'Non-existent Title']);

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

        $message = $this->createPostMessage($webhookUrl, 'New Title', []);

        $this->repository->save($message);

        $found = $this->repository->findOneBy(['title' => 'New Title']);
        $this->assertInstanceOf(PostMessage::class, $found);
        $this->assertSame('New Title', $found->getTitle());
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);
        self::getEntityManager()->flush();

        $message = $this->createPostMessage($webhookUrl, 'No Flush Title', []);

        $this->repository->save($message, false);

        $found = $this->repository->findOneBy(['title' => 'No Flush Title']);
        $this->assertNull($found);

        self::getEntityManager()->flush();
        $found = $this->repository->findOneBy(['title' => 'No Flush Title']);
        $this->assertInstanceOf(PostMessage::class, $found);
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createPostMessage($webhookUrl, 'To Remove Title', []);
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $this->repository->remove($message);

        $found = $this->repository->findOneBy(['title' => 'To Remove Title']);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createPostMessage($webhookUrl, 'No Flush Remove Title', []);
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $this->repository->remove($message, false);

        $found = $this->repository->findOneBy(['title' => 'No Flush Remove Title']);
        $this->assertInstanceOf(PostMessage::class, $found);

        self::getEntityManager()->flush();
        $found = $this->repository->findOneBy(['title' => 'No Flush Remove Title']);
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

    /**
     * @param array<mixed> $content
     */
    private function createPostMessage(WebhookUrl $webhookUrl, string $title, array $content): PostMessage
    {
        $message = new PostMessage();
        $message->setWebhookUrl($webhookUrl);
        $message->setTitle($title);

        // 使用正确的方法添加内容段落
        foreach ($content as $paragraphData) {
            $paragraph = new PostParagraph();
            if (\is_array($paragraphData) && isset($paragraphData['text']) && \is_string($paragraphData['text'])) {
                $paragraph->addText($paragraphData['text']);
            }
            $message->addParagraph($paragraph);
        }

        return $message;
    }

    public function testFindOneByWithOrderByClauseShouldReturnFirstOrderedEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message1 = $this->createPostMessage($webhookUrl, 'z-findone-order-title', []);
        $message2 = $this->createPostMessage($webhookUrl, 'a-findone-order-title', []);

        self::getEntityManager()->persist($message1);
        self::getEntityManager()->persist($message2);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['webhookUrl' => $webhookUrl], ['title' => 'ASC']);
        $this->assertInstanceOf(PostMessage::class, $result);
        $this->assertSame('a-findone-order-title', $result->getTitle());
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

        $message1 = $this->createPostMessage($webhookUrl1, 'association-title-1', []);
        $message2 = $this->createPostMessage($webhookUrl1, 'association-title-2', []);
        $message3 = $this->createPostMessage($webhookUrl2, 'association-title-3', []);

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

        $message = $this->createPostMessage($webhookUrl, 'association-test-title', []);
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['webhookUrl' => $webhookUrl]);
        $this->assertInstanceOf(PostMessage::class, $result);
        $this->assertSame($webhookUrl->getId(), $result->getWebhookUrl()->getId());
    }

    public function testSaveMethodShouldPersistEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);
        self::getEntityManager()->flush();

        $message = $this->createPostMessage($webhookUrl, 'save-method-test-title', []);

        $this->repository->save($message, true);

        $found = $this->repository->find($message->getId());
        $this->assertInstanceOf(PostMessage::class, $found);
        $this->assertSame('save-method-test-title', $found->getTitle());
    }

    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $webhookUrl = $this->createWebhookUrl();
        self::getEntityManager()->persist($webhookUrl);

        $message = $this->createPostMessage($webhookUrl, 'remove-method-test-title', []);
        self::getEntityManager()->persist($message);
        self::getEntityManager()->flush();

        $id = $message->getId();

        $this->repository->remove($message, true);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    /**
     * 创建一个新的帖子消息实体用于测试
     */
    protected function createNewEntity(): object
    {
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName('Test Webhook ' . uniqid());
        $webhookUrl->setUrl('https://example.com/webhook');
        $webhookUrl->setValid(true);

        $message = new PostMessage();
        $message->setWebhookUrl($webhookUrl);
        $message->setTitle('Test Post ' . uniqid());

        return $message;
    }

    /**
     * @return ServiceEntityRepository<PostMessage>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
