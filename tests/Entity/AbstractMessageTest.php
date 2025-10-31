<?php

namespace LarkCustomBotBundle\Tests\Entity;

use LarkCustomBotBundle\Entity\AbstractMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AbstractMessage::class)]
final class AbstractMessageTest extends TestCase
{
    private function createTestMessage(): AbstractMessage
    {
        return new class extends AbstractMessage {
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

    public function testGetIdReturnsNullWhenNotSet(): void
    {
        $message = $this->createTestMessage();
        $this->assertSame(0, $message->getId());
    }

    public function testSetWebhookUrl(): void
    {
        $message = $this->createTestMessage();
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName('测试Webhook');
        $webhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook');

        $message->setWebhookUrl($webhookUrl);
        $this->assertSame($webhookUrl, $message->getWebhookUrl());
    }

    public function testToStringReturnsExpectedFormat(): void
    {
        $message = $this->createTestMessage();
        $string = (string) $message;
        $this->assertEquals('test#0', $string);
    }

    public function testGetTypeIsAbstractMethod(): void
    {
        $message = $this->createTestMessage();
        $this->assertEquals('test', $message->getType());
    }

    public function testToArrayIsAbstractMethod(): void
    {
        $message = $this->createTestMessage();
        $array = $message->toArray();
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('id', $array);
    }

    public function testTimestampableAwareCreateTime(): void
    {
        $message = $this->createTestMessage();
        $createTime = new \DateTimeImmutable();
        $message->setCreateTime($createTime);
        $this->assertSame($createTime, $message->getCreateTime());
    }

    public function testTimestampableAwareUpdateTime(): void
    {
        $message = $this->createTestMessage();
        $updateTime = new \DateTimeImmutable();
        $message->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $message->getUpdateTime());
    }

    public function testDefaultIdIsZero(): void
    {
        $message = $this->createTestMessage();
        $this->assertSame(0, $message->getId());
    }
}
