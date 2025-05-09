<?php

namespace LarkCustomBotBundle\Tests\Entity;

use DateTime;
use LarkCustomBotBundle\Entity\ImageMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use PHPUnit\Framework\TestCase;

class ImageMessageTest extends TestCase
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
        $message = new ImageMessage();
        $this->assertEquals('image', $message->getType());
    }

    public function testGettersAndSetters_withValidData(): void
    {
        $message = new ImageMessage();
        $imageKey = 'img_7ea74629-9191-4176-998c-7f400cc0fb83';
        
        $message->setWebhookUrl($this->webhookUrl);
        $message->setImageKey($imageKey);
        
        $this->assertSame($this->webhookUrl, $message->getWebhookUrl());
        $this->assertEquals($imageKey, $message->getImageKey());
    }

    public function testToArray_returnsCorrectStructure(): void
    {
        $message = new ImageMessage();
        $imageKey = 'img_7ea74629-9191-4176-998c-7f400cc0fb83';
        
        $message->setWebhookUrl($this->webhookUrl);
        $message->setImageKey($imageKey);
        
        $array = $message->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('msg_type', $array);
        $this->assertArrayHasKey('content', $array);
        
        $this->assertEquals('image', $array['msg_type']);
        $this->assertIsArray($array['content']);
        $this->assertArrayHasKey('image_key', $array['content']);
        $this->assertEquals($imageKey, $array['content']['image_key']);
    }

    public function testSetImageKey_withEmptyString_shouldAcceptValue(): void
    {
        $message = new ImageMessage();
        $message->setImageKey('');
        $this->assertEquals('', $message->getImageKey());
    }
    
    public function testToArray_withEmptyImageKey_shouldIncludeEmptyImageKey(): void
    {
        $message = new ImageMessage();
        $message->setWebhookUrl($this->webhookUrl);
        $message->setImageKey('');
        
        $array = $message->toArray();
        
        $this->assertEquals('', $array['content']['image_key']);
    }

    public function testCreateTimeHandling_shouldSetAndGetCorrectly(): void
    {
        $message = new ImageMessage();
        $createTime = new DateTime();
        
        $message->setCreateTime($createTime);
        
        $this->assertSame($createTime, $message->getCreateTime());
    }

    public function testUpdateTimeHandling_shouldSetAndGetCorrectly(): void
    {
        $message = new ImageMessage();
        $updateTime = new DateTime();
        
        $message->setUpdateTime($updateTime);
        
        $this->assertSame($updateTime, $message->getUpdateTime());
    }
} 