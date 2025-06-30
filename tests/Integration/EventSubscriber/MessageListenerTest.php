<?php

namespace LarkCustomBotBundle\Tests\Integration\EventSubscriber;

use LarkCustomBotBundle\Entity\TextMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\EventSubscriber\MessageListener;
use LarkCustomBotBundle\Request\FeishuRobotRequest;
use LarkCustomBotBundle\Service\LarkRequestService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class MessageListenerTest extends TestCase
{
    private LarkRequestService&MockObject $larkRequestService;
    private MessageListener $listener;
    private WebhookUrl $webhookUrl;

    protected function setUp(): void
    {
        $this->larkRequestService = $this->createMock(LarkRequestService::class);
        $this->listener = new MessageListener($this->larkRequestService);
        
        $this->webhookUrl = new WebhookUrl();
        $this->webhookUrl->setName('测试Webhook');
        $this->webhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook');
    }

    public function testPostPersist_sendsMessageViaLarkRequestService(): void
    {
        $message = new TextMessage();
        $message->setWebhookUrl($this->webhookUrl);
        $message->setContent('测试消息');
        
        $this->larkRequestService
            ->expects($this->once())
            ->method('request')
            ->with($this->callback(function (FeishuRobotRequest $request) use ($message) {
                return $request->getMessage() === $message;
            }));
        
        $this->listener->postPersist($message);
    }

    public function testPostPersist_createsFeishuRobotRequest(): void
    {
        $message = new TextMessage();
        $message->setWebhookUrl($this->webhookUrl);
        $message->setContent('测试消息');
        
        $capturedRequest = null;
        
        $this->larkRequestService
            ->expects($this->once())
            ->method('request')
            ->with($this->callback(function (FeishuRobotRequest $request) use (&$capturedRequest) {
                $capturedRequest = $request;
                return true;
            }));
        
        $this->listener->postPersist($message);
        
        $this->assertInstanceOf(FeishuRobotRequest::class, $capturedRequest);
        $this->assertSame($message, $capturedRequest->getMessage());
    }

    public function testConstructor_acceptsLarkRequestService(): void
    {
        $listener = new MessageListener($this->larkRequestService);
        
        $this->assertInstanceOf(MessageListener::class, $listener);
    }

    public function testEntityListenerAttributes_areConfiguredCorrectly(): void
    {
        $reflection = new \ReflectionClass(MessageListener::class);
        $attributes = $reflection->getAttributes();
        
        $this->assertGreaterThan(0, count($attributes));
        
        $foundEntities = [];
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === 'Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener') {
                $args = $attribute->getArguments();
                if (isset($args['entity'])) {
                    $foundEntities[] = $args['entity'];
                }
            }
        }
        
        $expectedEntities = [
            'LarkCustomBotBundle\Entity\TextMessage',
            'LarkCustomBotBundle\Entity\ImageMessage',
            'LarkCustomBotBundle\Entity\InteractiveMessage',
            'LarkCustomBotBundle\Entity\PostMessage',
            'LarkCustomBotBundle\Entity\ShareChatMessage',
        ];
        
        foreach ($expectedEntities as $entity) {
            $this->assertContains($entity, $foundEntities);
        }
    }
}