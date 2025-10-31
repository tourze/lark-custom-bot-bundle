<?php

namespace LarkCustomBotBundle\Tests\Entity;

use LarkCustomBotBundle\Entity\PostMessage;
use LarkCustomBotBundle\ValueObject\PostParagraph;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(PostMessage::class)]
final class PostMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): PostMessage
    {
        return new PostMessage();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'title' => ['title', '测试标题'],
        ];
    }

    public function testGetTypeReturnsCorrectValue(): void
    {
        $message = new PostMessage();
        $this->assertEquals('post', $message->getType());
    }

    public function testAddParagraphAddsToContent(): void
    {
        $message = new PostMessage();
        $paragraph = new PostParagraph();
        $paragraph->addText('测试段落');

        $message->addParagraph($paragraph);

        $content = $message->getContent();
        $this->assertCount(1, $content);
        $this->assertInstanceOf(PostParagraph::class, $content[0]);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $message = new PostMessage();
        $message->setTitle('测试标题');

        $paragraph = new PostParagraph();
        $paragraph->addText('测试段落');
        $message->addParagraph($paragraph);

        $array = $message->toArray();
        $this->assertIsArray($array);

        $this->assertArrayHasKey('msg_type', $array);
        $this->assertArrayHasKey('content', $array);
        $this->assertEquals('post', $array['msg_type']);

        $content = $array['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('post', $content);

        $post = $content['post'];
        $this->assertIsArray($post);
        $this->assertArrayHasKey('zh_cn', $post);

        $zhCn = $post['zh_cn'];
        $this->assertIsArray($zhCn);
        $this->assertArrayHasKey('title', $zhCn);
        $this->assertArrayHasKey('content', $zhCn);
        $this->assertEquals('测试标题', $zhCn['title']);

        $zhCnContent = $zhCn['content'];
        $this->assertTrue(\is_array($zhCnContent) || $zhCnContent instanceof \Countable);
        $this->assertCount(1, $zhCnContent);
    }
}
