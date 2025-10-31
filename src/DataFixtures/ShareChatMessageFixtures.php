<?php

namespace LarkCustomBotBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use LarkCustomBotBundle\Entity\ShareChatMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;

class ShareChatMessageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName('Test Webhook');
        $webhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook-url');
        $webhookUrl->setRemark('Test webhook for share chat messages');
        $webhookUrl->setValid(true);

        $manager->persist($webhookUrl);

        $shareChatMessage1 = new ShareChatMessage();
        $shareChatMessage1->setChatId('oc_test_chat_id_1');
        $shareChatMessage1->setWebhookUrl($webhookUrl);

        $shareChatMessage2 = new ShareChatMessage();
        $shareChatMessage2->setChatId('oc_test_chat_id_2');
        $shareChatMessage2->setWebhookUrl($webhookUrl);

        $manager->persist($shareChatMessage1);
        $manager->persist($shareChatMessage2);

        $manager->flush();
    }
}
