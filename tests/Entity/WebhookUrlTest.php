<?php

namespace LarkCustomBotBundle\Tests\Entity;

use DateTime;
use LarkCustomBotBundle\Entity\WebhookUrl;
use PHPUnit\Framework\TestCase;

class WebhookUrlTest extends TestCase
{
    public function testGettersAndSetters_withValidData(): void
    {
        $webhook = new WebhookUrl();
        $name = 'TestWebhook';
        $url = 'https://open.feishu.cn/open-apis/bot/v2/hook/xxxxx';
        $remark = 'Test Remark';
        $valid = true;
        $createTime = new DateTime();
        $updateTime = new DateTime();

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

    public function testGetId_initialValue(): void
    {
        $webhook = new WebhookUrl();
        // ID应该初始化为0（或null，取决于具体实现）
        $this->assertEquals(0, $webhook->getId());
    }

    public function testRetrieveAdminArray_returnsCorrectStructure(): void
    {
        $webhook = new WebhookUrl();
        $name = 'TestWebhook';
        $url = 'https://open.feishu.cn/open-apis/bot/v2/hook/xxxxx';
        $remark = 'Test Remark';
        $createTime = new DateTime('2023-01-01 10:00:00');
        $updateTime = new DateTime('2023-01-02 10:00:00');

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

    public function testSetName_withEmptyString_shouldAcceptValue(): void
    {
        $webhook = new WebhookUrl();
        $webhook->setName('');
        $this->assertEquals('', $webhook->getName());
    }

    public function testSetRemark_withNull_shouldAcceptValue(): void
    {
        $webhook = new WebhookUrl();
        $webhook->setRemark(null);
        $this->assertNull($webhook->getRemark());
    }

    public function testSetValid_withNull_shouldAcceptValue(): void
    {
        $webhook = new WebhookUrl();
        $webhook->setValid(null);
        $this->assertNull($webhook->isValid());
    }

    public function testRetrieveAdminArray_withNullDates_shouldHandleNullValues(): void
    {
        $webhook = new WebhookUrl();
        $webhook->setName('TestWebhook');
        $webhook->setUrl('https://example.com');
        // 不设置日期值，应该能够正确处理

        $adminArray = $webhook->retrieveAdminArray();
        
        $this->assertNull($adminArray['createTime']);
        $this->assertNull($adminArray['updateTime']);
    }
} 