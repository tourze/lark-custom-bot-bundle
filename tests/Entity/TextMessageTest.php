<?php

namespace LarkCustomBotBundle\Tests\Entity;

use DateTime;
use LarkCustomBotBundle\Entity\TextMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use PHPUnit\Framework\TestCase;

class TextMessageTest extends TestCase
{
    private WebhookUrl $webhookUrl;

    protected function setUp(): void
    {
        $this->webhookUrl = new WebhookUrl();
        $this->webhookUrl->setName('测试Webhook');
        $this->webhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook');
    }

    public function testGetType_returnsCorrectValue(): void
    {
        $message = new TextMessage();
        $this->assertEquals('text', $message->getType());
    }

    public function testGettersAndSetters_withValidData(): void
    {
        $message = new TextMessage();
        $content = '这是一条测试消息';
        
        $message->setWebhookUrl($this->webhookUrl);
        $message->setContent($content);
        
        $this->assertSame($this->webhookUrl, $message->getWebhookUrl());
        $this->assertEquals($content, $message->getContent());
    }

    public function testToArray_returnsCorrectStructure(): void
    {
        $message = new TextMessage();
        $content = '这是一条测试消息';
        
        $message->setWebhookUrl($this->webhookUrl);
        $message->setContent($content);
        
        $array = $message->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('msg_type', $array);
        $this->assertArrayHasKey('content', $array);
        
        $this->assertEquals('text', $array['msg_type']);
        $this->assertIsArray($array['content']);
        $this->assertArrayHasKey('text', $array['content']);
        $this->assertEquals($content, $array['content']['text']);
    }

    public function testSetContent_withEmptyString_shouldAcceptValue(): void
    {
        $message = new TextMessage();
        $message->setContent('');
        $this->assertEquals('', $message->getContent());
    }
    
    public function testToArray_withEmptyContent_shouldIncludeEmptyContent(): void
    {
        $message = new TextMessage();
        $message->setWebhookUrl($this->webhookUrl);
        $message->setContent('');
        
        $array = $message->toArray();
        
        $this->assertEquals('', $array['content']['text']);
    }

    public function testCreateTimeHandling_shouldSetAndGetCorrectly(): void
    {
        $message = new TextMessage();
        $createTime = new DateTime();
        
        $message->setCreateTime($createTime);
        
        $this->assertSame($createTime, $message->getCreateTime());
    }

    public function testUpdateTimeHandling_shouldSetAndGetCorrectly(): void
    {
        $message = new TextMessage();
        $updateTime = new DateTime();
        
        $message->setUpdateTime($updateTime);
        
        $this->assertSame($updateTime, $message->getUpdateTime());
    }
} 