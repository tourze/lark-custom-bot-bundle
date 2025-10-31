<?php

namespace LarkCustomBotBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use LarkCustomBotBundle\Entity\WebhookUrl;

class WebhookUrlFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $webhookUrl1 = new WebhookUrl();
        $webhookUrl1->setName('Dev Webhook');
        $webhookUrl1->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/dev-webhook-url');
        $webhookUrl1->setRemark('Development environment webhook');
        $webhookUrl1->setValid(true);

        $webhookUrl2 = new WebhookUrl();
        $webhookUrl2->setName('Test Webhook');
        $webhookUrl2->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook-url');
        $webhookUrl2->setRemark('Testing environment webhook');
        $webhookUrl2->setValid(true);

        $webhookUrl3 = new WebhookUrl();
        $webhookUrl3->setName('Disabled Hook');
        $webhookUrl3->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/disabled-webhook-url');
        $webhookUrl3->setRemark('Disabled webhook for testing');
        $webhookUrl3->setValid(false);

        $manager->persist($webhookUrl1);
        $manager->persist($webhookUrl2);
        $manager->persist($webhookUrl3);

        $manager->flush();
    }
}
