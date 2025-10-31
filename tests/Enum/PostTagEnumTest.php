<?php

namespace LarkCustomBotBundle\Tests\Enum;

use LarkCustomBotBundle\Enum\PostTagEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(PostTagEnum::class)]
final class PostTagEnumTest extends AbstractEnumTestCase
{
    public function testCasesCount(): void
    {
        $cases = PostTagEnum::cases();
        $this->assertCount(6, $cases);
    }

    public function testAllCasesExist(): void
    {
        $cases = PostTagEnum::cases();

        $this->assertContains(PostTagEnum::TEXT, $cases);
        $this->assertContains(PostTagEnum::A, $cases);
        $this->assertContains(PostTagEnum::AT, $cases);
        $this->assertContains(PostTagEnum::IMG, $cases);
        $this->assertContains(PostTagEnum::MEDIA, $cases);
        $this->assertContains(PostTagEnum::EMOTION, $cases);
    }

    public function testEnumNames(): void
    {
        $this->assertSame('TEXT', PostTagEnum::TEXT->name);
        $this->assertSame('A', PostTagEnum::A->name);
        $this->assertSame('AT', PostTagEnum::AT->name);
        $this->assertSame('IMG', PostTagEnum::IMG->name);
        $this->assertSame('MEDIA', PostTagEnum::MEDIA->name);
        $this->assertSame('EMOTION', PostTagEnum::EMOTION->name);
    }

    public function testToArrayContainsRequiredKeys(): void
    {
        $array = PostTagEnum::TEXT->toArray();

        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame('text', $array['value']);
        $this->assertSame('纯文本', $array['label']);
    }

    public function testToSelectItemContainsAllKeys(): void
    {
        $item = PostTagEnum::IMG->toSelectItem();

        $expectedKeys = ['value', 'label', 'text', 'name'];
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $item);
        }

        $this->assertSame('img', $item['value']);
        $this->assertSame('图片', $item['label']);
        $this->assertSame('图片', $item['text']);
        $this->assertSame('图片', $item['name']);
    }

    public function testGenOptionsReturnsCorrectStructure(): void
    {
        $options = PostTagEnum::genOptions();

        $this->assertCount(6, $options);

        $expectedKeys = ['value', 'label', 'text', 'name'];
        foreach ($options as $option) {
            foreach ($expectedKeys as $key) {
                $this->assertArrayHasKey($key, $option);
            }
            $this->assertIsString($option['value']);
            $this->assertIsString($option['label']);
        }
    }

    public function testTryFromReturnsNullForInvalidValue(): void
    {
        $this->assertNull(PostTagEnum::tryFrom('invalid_value'));
        $this->assertNull(PostTagEnum::tryFrom(''));
        $this->assertNull(PostTagEnum::tryFrom('nonexistent'));
    }
}
