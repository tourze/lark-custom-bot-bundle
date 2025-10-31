<?php

namespace LarkCustomBotBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use LarkCustomBotBundle\Entity\TextMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;

class TextMessageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName('Test Webhook');
        $webhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook-url');
        $webhookUrl->setRemark('Test webhook for text messages');
        $webhookUrl->setValid(true);

        $manager->persist($webhookUrl);

        $textMessage1 = new TextMessage();
        $textMessage1->setContent('Hello, this is a test text message.');
        $textMessage1->setWebhookUrl($webhookUrl);

        $textMessage2 = new TextMessage();
        $textMessage2->setContent('Another test message with different content.');
        $textMessage2->setWebhookUrl($webhookUrl);

        $manager->persist($textMessage1);
        $manager->persist($textMessage2);

        $manager->flush();
    }
}
