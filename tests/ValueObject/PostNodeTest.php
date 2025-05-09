<?php

namespace LarkCustomBotBundle\Tests\ValueObject;

use LarkCustomBotBundle\ValueObject\PostNode;
use PHPUnit\Framework\TestCase;

class PostNodeTest extends TestCase
{
    public function testStaticFactoryMethods_withValidData(): void
    {
        $textContent = '测试内容';
        $userId = 'user_id_123';
        $url = 'https://example.com';
        $imageKey = 'image_key_123';
        
        $textNode = PostNode::text($textContent);
        $atNode = PostNode::at($userId);
        $linkNode = PostNode::link('Link Text', $url);
        $imageNode = PostNode::image($imageKey);
        
        $this->assertInstanceOf(PostNode::class, $textNode);
        $this->assertInstanceOf(PostNode::class, $atNode);
        $this->assertInstanceOf(PostNode::class, $linkNode);
        $this->assertInstanceOf(PostNode::class, $imageNode);
    }
    
    public function testJsonSerialize_withTextNode_returnsCorrectStructure(): void
    {
        $content = '测试内容';
        $node = PostNode::text($content);
        
        $json = json_encode($node);
        $array = json_decode($json, true);
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('tag', $array);
        $this->assertArrayHasKey('text', $array);
        
        $this->assertEquals('text', $array['tag']);
        $this->assertEquals($content, $array['text']);
    }
    
    public function testJsonSerialize_withAtNode_returnsCorrectStructure(): void
    {
        $userId = 'user_id_123';
        $node = PostNode::at($userId);
        
        $json = json_encode($node);
        $array = json_decode($json, true);
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('tag', $array);
        $this->assertArrayHasKey('user_id', $array);
        
        $this->assertEquals('at', $array['tag']);
        $this->assertEquals($userId, $array['user_id']);
    }
    
    public function testJsonSerialize_withAtNodeAndUsername_returnsCorrectStructure(): void
    {
        $userId = 'user_id_123';
        $userName = 'User Name';
        $node = PostNode::at($userId, $userName);
        
        $json = json_encode($node);
        $array = json_decode($json, true);
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('tag', $array);
        $this->assertArrayHasKey('user_id', $array);
        $this->assertArrayHasKey('user_name', $array);
        
        $this->assertEquals('at', $array['tag']);
        $this->assertEquals($userId, $array['user_id']);
        $this->assertEquals($userName, $array['user_name']);
    }
    
    public function testJsonSerialize_withImageNode_returnsCorrectStructure(): void
    {
        $imageKey = 'image_key_123';
        $node = PostNode::image($imageKey);
        
        $json = json_encode($node);
        $array = json_decode($json, true);
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('tag', $array);
        $this->assertArrayHasKey('image_key', $array);
        
        $this->assertEquals('img', $array['tag']);
        $this->assertEquals($imageKey, $array['image_key']);
    }
    
    public function testJsonSerialize_withLinkNode_returnsCorrectStructure(): void
    {
        $text = 'Link Text';
        $href = 'https://example.com';
        $node = PostNode::link($text, $href);
        
        $json = json_encode($node);
        $array = json_decode($json, true);
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('tag', $array);
        $this->assertArrayHasKey('text', $array);
        $this->assertArrayHasKey('href', $array);
        
        $this->assertEquals('a', $array['tag']);
        $this->assertEquals($text, $array['text']);
        $this->assertEquals($href, $array['href']);
    }
    
    public function testJsonSerialize_withMediaNode_returnsCorrectStructure(): void
    {
        $fileKey = 'file_key_123';
        $imageKey = 'image_key_123';
        $node = PostNode::media($fileKey, $imageKey);
        
        $json = json_encode($node);
        $array = json_decode($json, true);
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('tag', $array);
        $this->assertArrayHasKey('file_key', $array);
        $this->assertArrayHasKey('image_key', $array);
        
        $this->assertEquals('media', $array['tag']);
        $this->assertEquals($fileKey, $array['file_key']);
        $this->assertEquals($imageKey, $array['image_key']);
    }
    
    public function testJsonSerialize_withEmotionNode_returnsCorrectStructure(): void
    {
        $emoticon = 'emoticon_name';
        $node = PostNode::emotion($emoticon);
        
        $json = json_encode($node);
        $array = json_decode($json, true);
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('tag', $array);
        $this->assertArrayHasKey('emoticon', $array);
        
        $this->assertEquals('emotion', $array['tag']);
        $this->assertEquals($emoticon, $array['emoticon']);
    }
} 