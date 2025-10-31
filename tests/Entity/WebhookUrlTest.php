<?php

namespace LarkCustomBotBundle\Tests\Entity;

use LarkCustomBotBundle\Entity\WebhookUrl;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(WebhookUrl::class)]
final class WebhookUrlTest extends AbstractEntityTestCase
{
    protected function createEntity(): WebhookUrl
    {
        return new WebhookUrl();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name' => ['name', 'TestWebhook'],
            'url' => ['url', 'https://open.feishu.cn/open-apis/bot/v2/hook/xxxxx'],
            'remark' => ['remark', 'Test Remark'],
            'valid' => ['valid', true],
            'createTime' => ['createTime', new \DateTimeImmutable()],
            'updateTime' => ['updateTime', new \DateTimeImmutable()],
        ];
    }

    public function testGettersAndSettersWithValidData(): void
    {
        $webhook = new WebhookUrl();
        $name = 'TestWebhook';
        $url = 'https://open.feishu.cn/open-apis/bot/v2/hook/xxxxx';
        $remark = 'Test Remark';
        $valid = true;
        $createTime = new \DateTimeImmutable();
        $updateTime = new \DateTimeImmutable();

        $webhook->setName($name);
        $webhook->setUrl($url);
        $webhook->setRemark($remark);
        $webhook->setValid($valid);
        $webhook->setCreateTime($createTime);
        $webhook->setUpdateTime($updateTime);

        $this->assertEquals($name, $webhook->getName());
        $this->assertEquals($url, $webhook->getUrl());
        $this->assertEquals($remark, $webhook->getRemark());
        $this->assertEquals($valid, $webhook->isValid());
        $this->assertSame($createTime, $webhook->getCreateTime());
        $this->assertSame($updateTime, $webhook->getUpdateTime());
    }

    public function testGetIdInitialValue(): void
    {
        $webhook = new WebhookUrl();
        $this->assertEquals(0, $webhook->getId());
    }

    public function testRetrieveAdminArrayReturnsCorrectStructure(): void
    {
        $webhook = new WebhookUrl();
        $name = 'TestWebhook';
        $url = 'https://open.feishu.cn/open-apis/bot/v2/hook/xxxxx';
        $remark = 'Test Remark';
        $createTime = new \DateTimeImmutable('2023-01-01 10:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 10:00:00');

        $webhook->setName($name);
        $webhook->setUrl($url);
        $webhook->setRemark($remark);
        $webhook->setCreateTime($createTime);
        $webhook->setUpdateTime($updateTime);

        $adminArray = $webhook->retrieveAdminArray();
        $this->assertArrayHasKey('id', $adminArray);
        $this->assertArrayHasKey('name', $adminArray);
        $this->assertArrayHasKey('url', $adminArray);
        $this->assertArrayHasKey('remark', $adminArray);
        $this->assertArrayHasKey('createTime', $adminArray);
        $this->assertArrayHasKey('updateTime', $adminArray);

        $this->assertEquals($name, $adminArray['name']);
        $this->assertEquals($url, $adminArray['url']);
        $this->assertEquals($remark, $adminArray['remark']);
        $this->assertEquals($createTime->format('Y-m-d H:i:s'), $adminArray['createTime']);
        $this->assertEquals($updateTime->format('Y-m-d H:i:s'), $adminArray['updateTime']);
    }

    public function testSetNameWithEmptyStringShouldAcceptValue(): void
    {
        $webhook = new WebhookUrl();
        $webhook->setName('');
        $this->assertEquals('', $webhook->getName());
    }

    public function testSetRemarkWithNullShouldAcceptValue(): void
    {
        $webhook = new WebhookUrl();
        $webhook->setRemark(null);
        $this->assertNull($webhook->getRemark());
    }

    public function testSetValidWithNullShouldAcceptValue(): void
    {
        $webhook = new WebhookUrl();
        $webhook->setValid(null);
        $this->assertNull($webhook->isValid());
    }

    public function testRetrieveAdminArrayWithNullDatesShouldHandleNullValues(): void
    {
        $webhook = new WebhookUrl();
        $webhook->setName('TestWebhook');
        $webhook->setUrl('https://example.com');

        $adminArray = $webhook->retrieveAdminArray();

        $this->assertNull($adminArray['createTime']);
        $this->assertNull($adminArray['updateTime']);
    }
}
