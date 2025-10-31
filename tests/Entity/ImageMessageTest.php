<?php

namespace LarkCustomBotBundle\Tests\Entity;

use LarkCustomBotBundle\Entity\ImageMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(ImageMessage::class)]
final class ImageMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): ImageMessage
    {
        return new ImageMessage();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'imageKey' => ['imageKey', 'img_7ea74629-9191-4176-998c-7f400cc0fb83'],
        ];
    }

    public function testGetTypeReturnsCorrectValue(): void
    {
        $message = new ImageMessage();
        $this->assertEquals('image', $message->getType());
    }

    public function testGettersAndSettersWithValidData(): void
    {
        $message = new ImageMessage();
        $imageKey = 'img_7ea74629-9191-4176-998c-7f400cc0fb83';

        $message->setImageKey($imageKey);

        $this->assertEquals($imageKey, $message->getImageKey());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $message = new ImageMessage();
        $message->setImageKey('img_7ea74629-9191-4176-998c-7f400cc0fb83');

        $array = $message->toArray();
        $this->assertIsArray($array);

        $this->assertArrayHasKey('msg_type', $array);
        $this->assertArrayHasKey('content', $array);
        $this->assertEquals('image', $array['msg_type']);

        $content = $array['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('image_key', $content);
        $this->assertEquals('img_7ea74629-9191-4176-998c-7f400cc0fb83', $content['image_key']);
    }

    public function testGetIdReturnsZero(): void
    {
        $message = new ImageMessage();
        $this->assertEquals(0, $message->getId());
    }
}
