<?php

namespace LarkCustomBotBundle\Tests\Entity;

use LarkCustomBotBundle\Entity\ShareChatMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(ShareChatMessage::class)]
final class ShareChatMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): ShareChatMessage
    {
        return new ShareChatMessage();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'chatId' => ['chatId', 'oc_1234567890'],
        ];
    }

    public function testGetTypeReturnsCorrectValue(): void
    {
        $message = new ShareChatMessage();
        $this->assertEquals('share_chat', $message->getType());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $message = new ShareChatMessage();
        $message->setChatId('oc_1234567890');

        $array = $message->toArray();
        $this->assertIsArray($array);

        $this->assertArrayHasKey('msg_type', $array);
        $this->assertArrayHasKey('content', $array);
        $this->assertEquals('share_chat', $array['msg_type']);

        $content = $array['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('share_chat_id', $content);
        $this->assertEquals('oc_1234567890', $content['share_chat_id']);
    }
}
