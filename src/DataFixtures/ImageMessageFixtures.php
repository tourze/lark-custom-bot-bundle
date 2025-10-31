<?php

namespace LarkCustomBotBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use LarkCustomBotBundle\Entity\ImageMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;

class ImageMessageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName('Test Webhook');
        $webhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook-url');
        $webhookUrl->setRemark('Test webhook for image messages');
        $webhookUrl->setValid(true);

        $manager->persist($webhookUrl);

        $imageMessage1 = new ImageMessage();
        $imageMessage1->setImageKey('img_v2_test_image_key_1');
        $imageMessage1->setWebhookUrl($webhookUrl);

        $imageMessage2 = new ImageMessage();
        $imageMessage2->setImageKey('img_v2_test_image_key_2');
        $imageMessage2->setWebhookUrl($webhookUrl);

        $manager->persist($imageMessage1);
        $manager->persist($imageMessage2);

        $manager->flush();
    }
}
