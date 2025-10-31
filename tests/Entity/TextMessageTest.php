<?php

namespace LarkCustomBotBundle\Tests\Entity;

use LarkCustomBotBundle\Entity\TextMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(TextMessage::class)]
final class TextMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): TextMessage
    {
        return new TextMessage();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'content' => ['content', '这是一条测试消息'],
        ];
    }

    public function testGetTypeReturnsCorrectValue(): void
    {
        $message = new TextMessage();
        $this->assertEquals('text', $message->getType());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $message = new TextMessage();
        $message->setContent('这是一条测试消息');

        $array = $message->toArray();
        $this->assertIsArray($array);

        $this->assertArrayHasKey('msg_type', $array);
        $this->assertArrayHasKey('content', $array);
        $this->assertEquals('text', $array['msg_type']);

        $content = $array['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('text', $content);
        $this->assertEquals('这是一条测试消息', $content['text']);
    }
}
