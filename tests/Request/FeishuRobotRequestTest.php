<?php

namespace LarkCustomBotBundle\Tests\Request;

use LarkCustomBotBundle\Entity\TextMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Request\FeishuRobotRequest;
use PHPUnit\Framework\TestCase;

class FeishuRobotRequestTest extends TestCase
{
    private WebhookUrl $webhookUrl;
    private TextMessage $message;
    private FeishuRobotRequest $request;

    protected function setUp(): void
    {
        // 创建WebhookUrl实例
        $this->webhookUrl = new WebhookUrl();
        $this->webhookUrl->setName('测试Webhook');
        $this->webhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook');

        // 创建消息实例
        $this->message = new TextMessage();
        $this->message->setWebhookUrl($this->webhookUrl);
        $this->message->setContent('测试消息内容');

        // 创建请求实例
        $this->request = new FeishuRobotRequest();
        $this->request->setMessage($this->message);
    }

    public function testGetRequestPath_returnsWebhookUrl(): void
    {
        $expectedPath = 'https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook';
        $this->assertEquals($expectedPath, $this->request->getRequestPath());
    }

    public function testGetRequestOptions_returnsCorrectJsonStructure(): void
    {
        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('msg_type', $options['json']);
        $this->assertArrayHasKey('content', $options['json']);
        $this->assertEquals('text', $options['json']['msg_type']);
        $this->assertArrayHasKey('text', $options['json']['content']);
        $this->assertEquals('测试消息内容', $options['json']['content']['text']);
    }

    public function testSetAndGetMessage_shouldWorkCorrectly(): void
    {
        $newMessage = new TextMessage();
        $newMessage->setWebhookUrl($this->webhookUrl);
        $newMessage->setContent('新的测试消息');
        
        $this->request->setMessage($newMessage);
        
        $this->assertSame($newMessage, $this->request->getMessage());
    }

    public function testGetRequestPath_withDifferentWebhookUrl_shouldReturnCorrectPath(): void
    {
        // 创建不同的WebhookUrl
        $newWebhookUrl = new WebhookUrl();
        $newWebhookUrl->setName('另一个Webhook');
        $newWebhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/another-webhook');
        
        // 更新消息使用新的WebhookUrl
        $this->message->setWebhookUrl($newWebhookUrl);
        
        // 验证请求路径是否更新
        $expectedPath = 'https://open.feishu.cn/open-apis/bot/v2/hook/another-webhook';
        $this->assertEquals($expectedPath, $this->request->getRequestPath());
    }
} 