<?php

namespace LarkCustomBotBundle\Tests\Unit\Enum;

use LarkCustomBotBundle\Enum\PostTagEnum;
use PHPUnit\Framework\TestCase;

class PostTagEnumTest extends TestCase
{
    public function testEnumValues_areCorrect(): void
    {
        $this->assertEquals('text', PostTagEnum::TEXT->value);
        $this->assertEquals('a', PostTagEnum::A->value);
        $this->assertEquals('at', PostTagEnum::AT->value);
        $this->assertEquals('img', PostTagEnum::IMG->value);
        $this->assertEquals('media', PostTagEnum::MEDIA->value);
        $this->assertEquals('emotion', PostTagEnum::EMOTION->value);
    }

    public function testGetLabel_returnsCorrectLabels(): void
    {
        $this->assertEquals('纯文本', PostTagEnum::TEXT->getLabel());
        $this->assertEquals('超链接', PostTagEnum::A->getLabel());
        $this->assertEquals('@用户', PostTagEnum::AT->getLabel());
        $this->assertEquals('图片', PostTagEnum::IMG->getLabel());
        $this->assertEquals('媒体文件', PostTagEnum::MEDIA->getLabel());
        $this->assertEquals('表情', PostTagEnum::EMOTION->getLabel());
    }

    public function testFromValue_returnsCorrectEnum(): void
    {
        $this->assertSame(PostTagEnum::TEXT, PostTagEnum::from('text'));
        $this->assertSame(PostTagEnum::A, PostTagEnum::from('a'));
        $this->assertSame(PostTagEnum::AT, PostTagEnum::from('at'));
        $this->assertSame(PostTagEnum::IMG, PostTagEnum::from('img'));
        $this->assertSame(PostTagEnum::MEDIA, PostTagEnum::from('media'));
        $this->assertSame(PostTagEnum::EMOTION, PostTagEnum::from('emotion'));
    }

    public function testTryFrom_returnsNullForInvalidValue(): void
    {
        $this->assertNull(PostTagEnum::tryFrom('invalid'));
    }

    public function testCases_returnsAllEnumCases(): void
    {
        $cases = PostTagEnum::cases();
        
        $this->assertCount(6, $cases);
        $this->assertContains(PostTagEnum::TEXT, $cases);
        $this->assertContains(PostTagEnum::A, $cases);
        $this->assertContains(PostTagEnum::AT, $cases);
        $this->assertContains(PostTagEnum::IMG, $cases);
        $this->assertContains(PostTagEnum::MEDIA, $cases);
        $this->assertContains(PostTagEnum::EMOTION, $cases);
    }

    public function testItemTrait_toArray(): void
    {
        $array = PostTagEnum::TEXT->toArray();
        
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertEquals('text', $array['value']);
        $this->assertEquals('纯文本', $array['label']);
    }

    public function testItemTrait_toSelectItem(): void
    {
        $item = PostTagEnum::IMG->toSelectItem();
        
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertArrayHasKey('text', $item);
        $this->assertArrayHasKey('name', $item);
        
        $this->assertEquals('img', $item['value']);
        $this->assertEquals('图片', $item['label']);
        $this->assertEquals('图片', $item['text']);
        $this->assertEquals('图片', $item['name']);
    }

    public function testSelectTrait_genOptions(): void
    {
        $options = PostTagEnum::genOptions();
        
        $this->assertCount(6, $options);
        
        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('text', $option);
            $this->assertArrayHasKey('name', $option);
        }
    }

    public function testEnumName_isCorrect(): void
    {
        $this->assertEquals('TEXT', PostTagEnum::TEXT->name);
        $this->assertEquals('A', PostTagEnum::A->name);
        $this->assertEquals('AT', PostTagEnum::AT->name);
        $this->assertEquals('IMG', PostTagEnum::IMG->name);
        $this->assertEquals('MEDIA', PostTagEnum::MEDIA->name);
        $this->assertEquals('EMOTION', PostTagEnum::EMOTION->name);
    }
}