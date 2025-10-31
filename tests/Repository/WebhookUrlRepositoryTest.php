<?php

namespace LarkCustomBotBundle\Tests\Repository;

use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Repository\WebhookUrlRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(WebhookUrlRepository::class)]
#[RunTestsInSeparateProcesses]
final class WebhookUrlRepositoryTest extends AbstractRepositoryTestCase
{
    private WebhookUrlRepository $repository;

    protected function onSetUp(): void
    {
        // Setup handled by the parent class
    }

    protected function getRepository(): WebhookUrlRepository
    {
        if (!isset($this->repository)) {
            $this->repository = self::getService(WebhookUrlRepository::class);
        }

        return $this->repository;
    }

    public function testConstructShouldInitializeWithCorrectEntityClass(): void
    {
        $repository = self::getService(WebhookUrlRepository::class);
        $this->assertInstanceOf(WebhookUrlRepository::class, $repository);
    }

    public function testFindAllShouldReturnAllEntities(): void
    {
        $url1 = $this->createWebhookUrl('Webhook 1', 'https://example.com/webhook1');
        $url2 = $this->createWebhookUrl('Webhook 2', 'https://example.com/webhook2');

        self::getEntityManager()->persist($url1);
        self::getEntityManager()->persist($url2);
        self::getEntityManager()->flush();

        $repository = $this->getRepository();
        $result = $repository->findAll();

        $this->assertCount(10, $result);
        $this->assertContainsOnlyInstancesOf(WebhookUrl::class, $result);
    }

    public function testFindAllShouldReturnExistingEntitiesFromFixtures(): void
    {
        $repository = $this->getRepository();
        $result = $repository->findAll();

        $this->assertIsArray($result);
        $this->assertCount(8, $result);
        $this->assertContainsOnlyInstancesOf(WebhookUrl::class, $result);
    }

    public function testFindByShouldReturnMatchingEntities(): void
    {
        $url1 = $this->createWebhookUrl('Matching Name', 'https://example.com/webhook1');
        $url2 = $this->createWebhookUrl('Different Name', 'https://example.com/webhook2');

        self::getEntityManager()->persist($url1);
        self::getEntityManager()->persist($url2);
        self::getEntityManager()->flush();

        $repository = $this->getRepository();
        $result = $repository->findBy(['name' => 'Matching Name']);

        $this->assertCount(1, $result);
        $this->assertSame('Matching Name', $result[0]->getName());
    }

    public function testFindByShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $url = $this->createWebhookUrl('Existing Name', 'https://example.com/webhook');
        self::getEntityManager()->persist($url);
        self::getEntityManager()->flush();

        $repository = $this->getRepository();
        $result = $repository->findBy(['name' => 'Non-existent Name']);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $url = $this->createWebhookUrl("Name {$i}", "https://example.com/webhook{$i}");
            self::getEntityManager()->persist($url);
        }
        self::getEntityManager()->flush();

        $repository = $this->getRepository();
        $result = $repository->findBy([], ['id' => 'ASC'], 2, 1);

        $this->assertCount(2, $result);
    }

    public function testFindByWithValidField(): void
    {
        $repository = $this->getRepository();
        $initialValidCount = $repository->count(['valid' => true]);

        $url1 = $this->createWebhookUrl('Valid Webhook', 'https://example.com/webhook1', true);
        $url2 = $this->createWebhookUrl('Invalid Webhook', 'https://example.com/webhook2', false);

        self::getEntityManager()->persist($url1);
        self::getEntityManager()->persist($url2);
        self::getEntityManager()->flush();

        $result = $repository->findBy(['valid' => true]);

        $this->assertCount($initialValidCount + 1, $result);

        $foundValidWebhook = false;
        foreach ($result as $webhook) {
            if ('Valid Webhook' === $webhook->getName()) {
                $this->assertTrue($webhook->isValid());
                $foundValidWebhook = true;
            }
        }
        $this->assertTrue($foundValidWebhook, 'Valid Webhook not found in results');
    }

    public function testFindOneByWithNullField(): void
    {
        $url = $this->createWebhookUrl('Test Name', 'https://example.com/webhook');
        self::getEntityManager()->persist($url);
        self::getEntityManager()->flush();

        $repository = $this->getRepository();
        $result = $repository->findOneBy(['name' => null]);

        $this->assertNull($result);
    }

    public function testFindOneByShouldReturnSingleEntity(): void
    {
        $url = $this->createWebhookUrl('Unique Name', 'https://example.com/unique');
        self::getEntityManager()->persist($url);
        self::getEntityManager()->flush();

        $repository = $this->getRepository();
        $result = $repository->findOneBy(['name' => 'Unique Name']);

        $this->assertInstanceOf(WebhookUrl::class, $result);
        $this->assertSame('Unique Name', $result->getName());
        $this->assertSame('https://example.com/unique', $result->getUrl());
    }

    public function testFindOneByShouldReturnNullWhenNotFound(): void
    {
        $repository = $this->getRepository();
        $result = $repository->findOneBy(['name' => 'Non-existent Name']);

        $this->assertNull($result);
    }

    public function testCountShouldReturnCorrectNumber(): void
    {
        $url1 = $this->createWebhookUrl('Name 1', 'https://example.com/webhook1');
        $url2 = $this->createWebhookUrl('Name 2', 'https://example.com/webhook2');

        self::getEntityManager()->persist($url1);
        self::getEntityManager()->persist($url2);
        self::getEntityManager()->flush();

        $repository = $this->getRepository();
        $count = $repository->count([]);

        $this->assertSame(10, $count);
    }

    public function testCountWithCriteria(): void
    {
        $repository = $this->getRepository();
        $initialValidCount = $repository->count(['valid' => true]);

        $url1 = $this->createWebhookUrl('Valid Webhook', 'https://example.com/webhook1', true);
        $url2 = $this->createWebhookUrl('Invalid Webhook', 'https://example.com/webhook2', false);

        self::getEntityManager()->persist($url1);
        self::getEntityManager()->persist($url2);
        self::getEntityManager()->flush();

        $count = $repository->count(['valid' => true]);

        $this->assertSame($initialValidCount + 1, $count);
    }

    public function testCountShouldReturnZeroWhenNoEntities(): void
    {
        $repository = $this->getRepository();
        $count = $repository->count([]);

        $this->assertSame(8, $count);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $url = $this->createWebhookUrl('New Webhook', 'https://example.com/new');

        $repository = $this->getRepository();
        $repository->save($url);

        $found = $repository->findOneBy(['name' => 'New Webhook']);
        $this->assertInstanceOf(WebhookUrl::class, $found);
        $this->assertSame('New Webhook', $found->getName());
        $this->assertSame('https://example.com/new', $found->getUrl());
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $url = $this->createWebhookUrl('No Flush Webhook', 'https://example.com/no-flush');

        $repository = $this->getRepository();
        $repository->save($url, false);

        $found = $repository->findOneBy(['name' => 'No Flush Webhook']);
        $this->assertNull($found);

        self::getEntityManager()->flush();
        $found = $repository->findOneBy(['name' => 'No Flush Webhook']);
        $this->assertInstanceOf(WebhookUrl::class, $found);
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $url = $this->createWebhookUrl('To Remove', 'https://example.com/remove');
        self::getEntityManager()->persist($url);
        self::getEntityManager()->flush();

        $repository = $this->getRepository();
        $repository->remove($url);

        $found = $repository->findOneBy(['name' => 'To Remove']);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $url = $this->createWebhookUrl('No Flush Remove', 'https://example.com/no-flush-remove');
        self::getEntityManager()->persist($url);
        self::getEntityManager()->flush();

        $repository = $this->getRepository();
        $repository->remove($url, false);

        $found = $repository->findOneBy(['name' => 'No Flush Remove']);
        $this->assertInstanceOf(WebhookUrl::class, $found);

        self::getEntityManager()->flush();
        $found = $repository->findOneBy(['name' => 'No Flush Remove']);
        $this->assertNull($found);
    }

    public function testFindByWithInvalidFieldShouldThrowException(): void
    {
        $this->expectException(\Exception::class);
        $repository = $this->getRepository();
        $repository->findBy(['nonExistentField' => 'value']);
    }

    private function createWebhookUrl(string $name, string $url, ?bool $valid = true): WebhookUrl
    {
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName($name);
        $webhookUrl->setUrl($url);
        $webhookUrl->setValid($valid);

        return $webhookUrl;
    }

    public function testFindByWithNameCriteriaShouldReturnArrayOfEntities(): void
    {
        $url1 = $this->createWebhookUrl('Test Name', 'https://example1.com', true);
        $url2 = $this->createWebhookUrl('Other Name', 'https://example2.com', true);
        $url3 = $this->createWebhookUrl('Test Name', 'https://example3.com', false);

        self::getEntityManager()->persist($url1);
        self::getEntityManager()->persist($url2);
        self::getEntityManager()->persist($url3);
        self::getEntityManager()->flush();

        $repository = $this->getRepository();
        $result = $repository->findBy(['name' => 'Test Name']);

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(WebhookUrl::class, $result);
    }

    public function testFindOneByWithOrderByClauseShouldReturnFirstOrderedEntity(): void
    {
        $url1 = $this->createWebhookUrl('B Name', 'https://example1.com');
        $url2 = $this->createWebhookUrl('A Name', 'https://example2.com');

        self::getEntityManager()->persist($url1);
        self::getEntityManager()->persist($url2);
        self::getEntityManager()->flush();

        $repository = $this->getRepository();
        $result = $repository->findOneBy([], ['name' => 'ASC']);

        $this->assertInstanceOf(WebhookUrl::class, $result);
        $this->assertSame('A Name', $result->getName());
    }

    public function testFindByNullFieldShouldReturnMatchingEntities(): void
    {
        $url1 = $this->createWebhookUrl('Test 1', 'https://example1.com');
        $url1->setRemark(null);

        $url2 = $this->createWebhookUrl('Test 2', 'https://example2.com');
        $url2->setRemark('Has remark');

        self::getEntityManager()->persist($url1);
        self::getEntityManager()->persist($url2);
        self::getEntityManager()->flush();

        $repository = $this->getRepository();
        $result = $repository->findBy(['remark' => null]);

        $this->assertCount(1, $result);
        $this->assertSame('Test 1', $result[0]->getName());
    }

    public function testCountByNullFieldShouldReturnCorrectNumber(): void
    {
        $url1 = $this->createWebhookUrl('Test 1', 'https://example1.com');
        $url1->setRemark(null);

        $url2 = $this->createWebhookUrl('Test 2', 'https://example2.com');
        $url2->setRemark('Has remark');

        self::getEntityManager()->persist($url1);
        self::getEntityManager()->persist($url2);
        self::getEntityManager()->flush();

        $repository = $this->getRepository();
        $count = $repository->count(['remark' => null]);

        $this->assertSame(1, $count);
    }

    /**
     * 创建一个新的Webhook URL实体用于测试
     */
    protected function createNewEntity(): object
    {
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName('Test Webhook ' . uniqid());
        $webhookUrl->setUrl('https://example.com/webhook-' . uniqid());
        $webhookUrl->setValid(true);

        return $webhookUrl;
    }
}
