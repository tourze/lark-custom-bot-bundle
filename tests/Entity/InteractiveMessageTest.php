<?php

namespace LarkCustomBotBundle\Tests\Entity;

use LarkCustomBotBundle\Entity\InteractiveMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(InteractiveMessage::class)]
final class InteractiveMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): InteractiveMessage
    {
        return new InteractiveMessage();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            ['card', [
                'config' => ['wide_screen_mode' => true],
                'elements' => [
                    [
                        'tag' => 'div',
                        'text' => [
                            'content' => '测试内容',
                            'tag' => 'lark_md',
                        ],
                    ],
                ],
            ]],
        ];
    }

    public function testGetTypeReturnsCorrectValue(): void
    {
        $message = new InteractiveMessage();
        $this->assertEquals('interactive', $message->getType());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $message = new InteractiveMessage();
        $card = [
            'config' => ['wide_screen_mode' => true],
            'elements' => [
                [
                    'tag' => 'div',
                    'text' => [
                        'content' => '测试内容',
                        'tag' => 'lark_md',
                    ],
                ],
            ],
        ];
        $message->setCard($card);

        $array = $message->toArray();

        $this->assertArrayHasKey('msg_type', $array);
        $this->assertArrayHasKey('card', $array);
        $this->assertEquals('interactive', $array['msg_type']);
        $this->assertEquals($card, $array['card']);
    }
}
