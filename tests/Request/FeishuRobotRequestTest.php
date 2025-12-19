<?php

namespace LarkCustomBotBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use LarkCustomBotBundle\Entity\TextMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Request\FeishuRobotRequest;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(FeishuRobotRequest::class)] final class FeishuRobotRequestTest extends RequestTestCase
{
    private WebhookUrl $webhookUrl;

    private TextMessage $message;

    private FeishuRobotRequest $request;

    protected function onSetUp(): void
    {
        // 创建WebhookUrl实例
        $this->webhookUrl = new WebhookUrl();
        $this->webhookUrl->setName('测试Webhook');
        $this->webhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook');

        // 创建消息实例
        $this->message = new TextMessage();
        $this->message->setWebhookUrl($this->webhookUrl);
        $this->message->setContent('测试消息内容');

        // 直接实例化请求类
        // FeishuRobotRequest 是一个简单的请求类，不是服务容器中的服务，直接实例化是正确的方式
        $this->request = new FeishuRobotRequest();
        $this->request->setMessage($this->message);
    }

    public function testGetRequestPathReturnsWebhookUrl(): void
    {
        $expectedPath = 'https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook';
        $this->assertEquals($expectedPath, $this->request->getRequestPath());
    }

    public function testGetRequestOptionsReturnsCorrectJsonStructure(): void
    {
        $options = $this->request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertArrayHasKey('msg_type', $jsonData);
        $this->assertArrayHasKey('content', $jsonData);
        $this->assertEquals('text', $jsonData['msg_type']);

        $content = $jsonData['content'];
        $this->assertIsArray($content);
        $this->assertArrayHasKey('text', $content);
        $this->assertEquals('测试消息内容', $content['text']);
    }

    public function testSetAndGetMessageShouldWorkCorrectly(): void
    {
        $newMessage = new TextMessage();
        $newMessage->setWebhookUrl($this->webhookUrl);
        $newMessage->setContent('新的测试消息');

        $this->request->setMessage($newMessage);

        $this->assertSame($newMessage, $this->request->getMessage());
    }

    public function testGetRequestPathWithDifferentWebhookUrlShouldReturnCorrectPath(): void
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
