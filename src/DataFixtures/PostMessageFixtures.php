<?php

namespace LarkCustomBotBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use LarkCustomBotBundle\Entity\PostMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\ValueObject\PostParagraph;

class PostMessageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName('Test Webhook');
        $webhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook-url');
        $webhookUrl->setRemark('Test webhook for post messages');
        $webhookUrl->setValid(true);

        $manager->persist($webhookUrl);

        $postMessage1 = new PostMessage();
        $postMessage1->setTitle('Test Post Message');
        $postMessage1->setWebhookUrl($webhookUrl);

        $paragraph1 = new PostParagraph();
        $paragraph1->addText('Hello, this is a test post message with ');
        $paragraph1->addText('rich text content. ');

        $postMessage1->addParagraph($paragraph1);

        $postMessage2 = new PostMessage();
        $postMessage2->setTitle('Another Post Message');
        $postMessage2->setWebhookUrl($webhookUrl);

        $paragraph2 = new PostParagraph();
        $paragraph2->addText('This is another test paragraph with ');
        $paragraph2->addText('multiple text nodes.');

        $postMessage2->addParagraph($paragraph2);

        $manager->persist($postMessage1);
        $manager->persist($postMessage2);

        $manager->flush();
    }
}
