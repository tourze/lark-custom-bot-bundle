<?php

namespace LarkCustomBotBundle\Tests\Entity;

use DateTime;
use LarkCustomBotBundle\Entity\ShareChatMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use PHPUnit\Framework\TestCase;

class ShareChatMessageTest extends TestCase
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
        $message = new ShareChatMessage();
        $this->assertEquals('share_chat', $message->getType());
    }

    public function testGettersAndSetters_withValidData(): void
    {
        $message = new ShareChatMessage();
        $shareId = 'oc_f5b1a7eb27ae2c7b6adc2a74faf339ff';
        
        $message->setWebhookUrl($this->webhookUrl);
        $message->setChatId($shareId);
        
        $this->assertSame($this->webhookUrl, $message->getWebhookUrl());
        $this->assertEquals($shareId, $message->getChatId());
    }

    public function testToArray_returnsCorrectStructure(): void
    {
        $message = new ShareChatMessage();
        $shareId = 'oc_f5b1a7eb27ae2c7b6adc2a74faf339ff';
        
        $message->setWebhookUrl($this->webhookUrl);
        $message->setChatId($shareId);
        
        $array = $message->toArray();
        $this->assertArrayHasKey('msg_type', $array);
        $this->assertArrayHasKey('content', $array);
        
        $this->assertEquals('share_chat', $array['msg_type']);
        $this->assertArrayHasKey('share_chat_id', $array['content']);
        $this->assertEquals($shareId, $array['content']['share_chat_id']);
    }

    public function testSetShareChatId_withEmptyString_shouldAcceptValue(): void
    {
        $message = new ShareChatMessage();
        $message->setChatId('');
        $this->assertEquals('', $message->getChatId());
    }
    
    public function testToArray_withEmptyShareChatId_shouldIncludeEmptyId(): void
    {
        $message = new ShareChatMessage();
        $message->setWebhookUrl($this->webhookUrl);
        $message->setChatId('');
        
        $array = $message->toArray();
        
        $this->assertEquals('', $array['content']['share_chat_id']);
    }

    public function testCreateTimeHandling_shouldSetAndGetCorrectly(): void
    {
        $message = new ShareChatMessage();
        $createTime = new DateTime();
        
        $message->setCreateTime($createTime);
        
        $this->assertSame($createTime, $message->getCreateTime());
    }

    public function testUpdateTimeHandling_shouldSetAndGetCorrectly(): void
    {
        $message = new ShareChatMessage();
        $updateTime = new DateTime();
        
        $message->setUpdateTime($updateTime);
        
        $this->assertSame($updateTime, $message->getUpdateTime());
    }
} 