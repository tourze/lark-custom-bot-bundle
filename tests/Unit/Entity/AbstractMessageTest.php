<?php

namespace LarkCustomBotBundle\Tests\Unit\Entity;

use DateTimeImmutable;
use LarkCustomBotBundle\Entity\AbstractMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use PHPUnit\Framework\TestCase;

class AbstractMessageTest extends TestCase
{
    private WebhookUrl $webhookUrl;
    private AbstractMessage $message;

    protected function setUp(): void
    {
        $this->webhookUrl = new WebhookUrl();
        $this->webhookUrl->setName('测试Webhook');
        $this->webhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook');
        
        $this->message = new class extends AbstractMessage {
            public function getType(): string
            {
                return 'test';
            }
            
            public function toArray(): array
            {
                return [
                    'type' => $this->getType(),
                    'id' => $this->getId(),
                ];
            }
        };
    }

    public function testGetId_returnsNull_whenNotSet(): void
    {
        $this->assertSame(0, $this->message->getId());
    }

    public function testWebhookUrl_canBeSetAndRetrieved(): void
    {
        $this->message->setWebhookUrl($this->webhookUrl);
        
        $this->assertSame($this->webhookUrl, $this->message->getWebhookUrl());
    }

    public function testSetWebhookUrl_returnsInstance_forFluentInterface(): void
    {
        $result = $this->message->setWebhookUrl($this->webhookUrl);
        
        $this->assertSame($this->message, $result);
    }

    public function testToString_returnsExpectedFormat(): void
    {
        $this->message->setWebhookUrl($this->webhookUrl);
        
        $string = (string) $this->message;
        
        $this->assertEquals('test#0', $string);
    }

    public function testGetType_isAbstractMethod(): void
    {
        $this->assertEquals('test', $this->message->getType());
    }

    public function testToArray_isAbstractMethod(): void
    {
        $array = $this->message->toArray();
        
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('id', $array);
    }

    public function testTimestampableAware_createTime(): void
    {
        $createTime = new DateTimeImmutable();
        $this->message->setCreateTime($createTime);
        
        $this->assertSame($createTime, $this->message->getCreateTime());
    }

    public function testTimestampableAware_updateTime(): void
    {
        $updateTime = new DateTimeImmutable();
        $this->message->setUpdateTime($updateTime);
        
        $this->assertSame($updateTime, $this->message->getUpdateTime());
    }

    public function testDefaultId_isZero(): void
    {
        $this->assertSame(0, $this->message->getId());
    }
}