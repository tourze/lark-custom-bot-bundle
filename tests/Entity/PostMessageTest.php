<?php

namespace LarkCustomBotBundle\Tests\Entity;

use LarkCustomBotBundle\Entity\PostMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\ValueObject\PostParagraph;
use PHPUnit\Framework\TestCase;

class PostMessageTest extends TestCase
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
        $message = new PostMessage();
        $this->assertEquals('post', $message->getType());
    }

    public function testGettersAndSetters_withValidData(): void
    {
        $message = new PostMessage();
        $title = '测试标题';
        
        $message->setWebhookUrl($this->webhookUrl);
        $message->setTitle($title);
        
        $this->assertSame($this->webhookUrl, $message->getWebhookUrl());
        $this->assertEquals($title, $message->getTitle());
    }

    public function testAddParagraph_addsParagraphToContent(): void
    {
        $message = new PostMessage();
        $paragraph1 = new PostParagraph();
        $paragraph1->addText('段落1内容');
        
        $paragraph2 = new PostParagraph();
        $paragraph2->addText('段落2内容');
        
        $message->addParagraph($paragraph1);
        $message->addParagraph($paragraph2);
        
        $paragraphs = $message->getContent();
        
        $this->assertCount(2, $paragraphs);
        $this->assertSame($paragraph1, $paragraphs[0]);
        $this->assertSame($paragraph2, $paragraphs[1]);
    }

    public function testToArray_returnsCorrectStructure(): void
    {
        $message = new PostMessage();
        $title = '测试标题';
        
        $message->setWebhookUrl($this->webhookUrl);
        $message->setTitle($title);
        
        $paragraph = new PostParagraph();
        $paragraph->addText('测试内容');
        $message->addParagraph($paragraph);
        
        $array = $message->toArray();
        $this->assertArrayHasKey('msg_type', $array);
        $this->assertArrayHasKey('content', $array);
        
        $this->assertEquals('post', $array['msg_type']);
        $this->assertArrayHasKey('post', $array['content']);
        $this->assertArrayHasKey('zh_cn', $array['content']['post']);
        $this->assertArrayHasKey('title', $array['content']['post']['zh_cn']);
        $this->assertArrayHasKey('content', $array['content']['post']['zh_cn']);
        
        $this->assertEquals($title, $array['content']['post']['zh_cn']['title']);
        $this->assertCount(1, $array['content']['post']['zh_cn']['content']);
    }

    public function testToArray_withMultipleParagraphs_returnsCorrectStructure(): void
    {
        $message = new PostMessage();
        $message->setWebhookUrl($this->webhookUrl);
        $message->setTitle('测试标题');
        
        $paragraph1 = new PostParagraph();
        $paragraph1->addText('段落1内容');
        
        $paragraph2 = new PostParagraph();
        $paragraph2->addText('段落2内容');
        
        $message->addParagraph($paragraph1);
        $message->addParagraph($paragraph2);
        
        $array = $message->toArray();
        
        $this->assertCount(2, $array['content']['post']['zh_cn']['content']);
    }

    public function testSetTitle_withEmptyString_shouldAcceptValue(): void
    {
        $message = new PostMessage();
        $message->setTitle('');
        $this->assertEquals('', $message->getTitle());
    }
    
    public function testToArray_withEmptyTitle_shouldIncludeEmptyTitle(): void
    {
        $message = new PostMessage();
        $message->setWebhookUrl($this->webhookUrl);
        $message->setTitle('');
        
        $paragraph = new PostParagraph();
        $paragraph->addText('测试内容');
        $message->addParagraph($paragraph);
        
        $array = $message->toArray();
        
        $this->assertEquals('', $array['content']['post']['zh_cn']['title']);
    }

    public function testToArray_withNoParagraphs_returnsEmptyContentArray(): void
    {
        $message = new PostMessage();
        $message->setWebhookUrl($this->webhookUrl);
        $message->setTitle('测试标题');
        
        $array = $message->toArray();
        $this->assertEmpty($array['content']['post']['zh_cn']['content']);
    }
} 