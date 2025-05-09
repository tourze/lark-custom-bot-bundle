<?php

namespace LarkCustomBotBundle\Tests\Entity;

use DateTime;
use LarkCustomBotBundle\Entity\InteractiveMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use PHPUnit\Framework\TestCase;

class InteractiveMessageTest extends TestCase
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
        $message = new InteractiveMessage();
        $this->assertEquals('interactive', $message->getType());
    }

    public function testGettersAndSetters_withValidData(): void
    {
        $message = new InteractiveMessage();
        $cardContent = [
            'config' => [
                'wide_screen_mode' => true
            ],
            'header' => [
                'title' => [
                    'tag' => 'plain_text',
                    'content' => '测试交互卡片'
                ]
            ],
            'elements' => [
                [
                    'tag' => 'div',
                    'text' => [
                        'tag' => 'plain_text',
                        'content' => '这是卡片内容'
                    ]
                ]
            ]
        ];
        
        $message->setWebhookUrl($this->webhookUrl);
        $message->setCard($cardContent);
        
        $this->assertSame($this->webhookUrl, $message->getWebhookUrl());
        $this->assertEquals($cardContent, $message->getCard());
    }

    public function testToArray_returnsCorrectStructure(): void
    {
        $message = new InteractiveMessage();
        $cardContent = [
            'config' => [
                'wide_screen_mode' => true
            ],
            'header' => [
                'title' => [
                    'tag' => 'plain_text',
                    'content' => '测试交互卡片'
                ]
            ],
            'elements' => [
                [
                    'tag' => 'div',
                    'text' => [
                        'tag' => 'plain_text',
                        'content' => '这是卡片内容'
                    ]
                ]
            ]
        ];
        
        $message->setWebhookUrl($this->webhookUrl);
        $message->setCard($cardContent);
        
        $array = $message->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('msg_type', $array);
        $this->assertArrayHasKey('card', $array);
        
        $this->assertEquals('interactive', $array['msg_type']);
        $this->assertEquals($cardContent, $array['card']);
    }

    public function testSetCardContent_withEmptyArray_shouldAcceptValue(): void
    {
        $message = new InteractiveMessage();
        $message->setCard([]);
        $this->assertEquals([], $message->getCard());
    }
    
    public function testToArray_withEmptyCardContent_shouldIncludeEmptyCard(): void
    {
        $message = new InteractiveMessage();
        $message->setWebhookUrl($this->webhookUrl);
        $message->setCard([]);
        
        $array = $message->toArray();
        
        $this->assertEquals([], $array['card']);
    }

    public function testCreateTimeHandling_shouldSetAndGetCorrectly(): void
    {
        $message = new InteractiveMessage();
        $createTime = new DateTime();
        
        $message->setCreateTime($createTime);
        
        $this->assertSame($createTime, $message->getCreateTime());
    }

    public function testUpdateTimeHandling_shouldSetAndGetCorrectly(): void
    {
        $message = new InteractiveMessage();
        $updateTime = new DateTime();
        
        $message->setUpdateTime($updateTime);
        
        $this->assertSame($updateTime, $message->getUpdateTime());
    }
    
    public function testSetCardContent_withComplexStructure_shouldHandleCorrectly(): void
    {
        $message = new InteractiveMessage();
        $complexCard = [
            'config' => [
                'wide_screen_mode' => true,
                'enable_forward' => true
            ],
            'header' => [
                'title' => [
                    'tag' => 'plain_text',
                    'content' => '测试卡片'
                ],
                'template' => 'blue'
            ],
            'elements' => [
                [
                    'tag' => 'div',
                    'text' => [
                        'tag' => 'lark_md',
                        'content' => '**加粗内容**普通内容'
                    ]
                ],
                [
                    'tag' => 'action',
                    'actions' => [
                        [
                            'tag' => 'button',
                            'text' => [
                                'tag' => 'plain_text',
                                'content' => '点击按钮'
                            ],
                            'type' => 'primary',
                            'value' => [
                                'key1' => 'value1'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        
        $message->setCard($complexCard);
        
        $this->assertEquals($complexCard, $message->getCard());
        
        $array = $message->toArray();
        $this->assertEquals($complexCard, $array['card']);
    }
} 