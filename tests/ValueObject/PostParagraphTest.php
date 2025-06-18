<?php

namespace LarkCustomBotBundle\Tests\ValueObject;

use LarkCustomBotBundle\ValueObject\PostNode;
use LarkCustomBotBundle\ValueObject\PostParagraph;
use PHPUnit\Framework\TestCase;

class PostParagraphTest extends TestCase
{
    public function testAddNode_addsNodeToCollection(): void
    {
        $paragraph = new PostParagraph();
        
        $text = '测试文本';
        $userId = 'user_123';
        
        $paragraph->addNode(PostNode::text($text));
        $paragraph->addNode(PostNode::at($userId));
        
        // 验证序列化结果包含两个节点
        $json = json_encode($paragraph);
        $array = json_decode($json, true);
        
        $this->assertCount(2, $array);
        $this->assertEquals('text', $array[0]['tag']);
        $this->assertEquals($text, $array[0]['text']);
        $this->assertEquals('at', $array[1]['tag']);
        $this->assertEquals($userId, $array[1]['user_id']);
    }
    
    public function testJsonSerialize_withNodes_returnsCorrectStructure(): void
    {
        $paragraph = new PostParagraph();
        
        $paragraph->addNode(PostNode::text('文本内容'));
        $paragraph->addNode(PostNode::at('user_123'));
        
        $json = json_encode($paragraph);
        $array = json_decode($json, true);
        $this->assertCount(2, $array);
        
        $this->assertEquals('text', $array[0]['tag']);
        $this->assertEquals('文本内容', $array[0]['text']);
        
        $this->assertEquals('at', $array[1]['tag']);
        $this->assertEquals('user_123', $array[1]['user_id']);
    }
    
    public function testJsonSerialize_withEmptyNodes_returnsEmptyArray(): void
    {
        $paragraph = new PostParagraph();
        
        $json = json_encode($paragraph);
        $array = json_decode($json, true);
        $this->assertEmpty($array);
    }
    
    public function testAddText_addsTextNode(): void
    {
        $paragraph = new PostParagraph();
        $text = '这是文本内容';
        
        $paragraph->addText($text);
        
        $json = json_encode($paragraph);
        $array = json_decode($json, true);
        
        $this->assertCount(1, $array);
        $this->assertEquals('text', $array[0]['tag']);
        $this->assertEquals($text, $array[0]['text']);
    }
    
    public function testAddAt_addsAtNode(): void
    {
        $paragraph = new PostParagraph();
        $userId = 'user_123';
        
        $paragraph->addAt($userId);
        
        $json = json_encode($paragraph);
        $array = json_decode($json, true);
        
        $this->assertCount(1, $array);
        $this->assertEquals('at', $array[0]['tag']);
        $this->assertEquals($userId, $array[0]['user_id']);
    }
    
    public function testAddImage_addsImageNode(): void
    {
        $paragraph = new PostParagraph();
        $imageKey = 'image_key_123';
        
        $paragraph->addImage($imageKey);
        
        $json = json_encode($paragraph);
        $array = json_decode($json, true);
        
        $this->assertCount(1, $array);
        $this->assertEquals('img', $array[0]['tag']);
        $this->assertEquals($imageKey, $array[0]['image_key']);
    }
    
    public function testAddLink_addsLinkNode(): void
    {
        $paragraph = new PostParagraph();
        $text = '链接文本';
        $href = 'https://example.com';
        
        $paragraph->addLink($text, $href);
        
        $json = json_encode($paragraph);
        $array = json_decode($json, true);
        
        $this->assertCount(1, $array);
        $this->assertEquals('a', $array[0]['tag']);
        $this->assertEquals($text, $array[0]['text']);
        $this->assertEquals($href, $array[0]['href']);
    }
    
    public function testAddMultipleNodes_maintainsOrder(): void
    {
        $paragraph = new PostParagraph();
        
        $paragraph->addText('文本1');
        $paragraph->addLink('链接', 'https://example.com');
        $paragraph->addAt('user_123');
        $paragraph->addImage('image_123');
        
        $json = json_encode($paragraph);
        $array = json_decode($json, true);
        
        $this->assertCount(4, $array);
        $this->assertEquals('text', $array[0]['tag']);
        $this->assertEquals('a', $array[1]['tag']);
        $this->assertEquals('at', $array[2]['tag']);
        $this->assertEquals('img', $array[3]['tag']);
    }
} 