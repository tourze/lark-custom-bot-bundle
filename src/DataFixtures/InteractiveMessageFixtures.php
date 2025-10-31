<?php

namespace LarkCustomBotBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use LarkCustomBotBundle\Entity\InteractiveMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;

class InteractiveMessageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $webhookUrl = new WebhookUrl();
        $webhookUrl->setName('Test Webhook');
        $webhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook-url');
        $webhookUrl->setRemark('Test webhook for interactive messages');
        $webhookUrl->setValid(true);

        $manager->persist($webhookUrl);

        $interactiveMessage1 = new InteractiveMessage();
        $interactiveMessage1->setCard([
            'header' => [
                'title' => [
                    'tag' => 'plain_text',
                    'content' => 'Test Card Title',
                ],
            ],
            'elements' => [
                [
                    'tag' => 'div',
                    'text' => [
                        'tag' => 'plain_text',
                        'content' => 'This is a test interactive message.',
                    ],
                ],
            ],
        ]);
        $interactiveMessage1->setWebhookUrl($webhookUrl);

        $interactiveMessage2 = new InteractiveMessage();
        $interactiveMessage2->setCard([
            'header' => [
                'title' => [
                    'tag' => 'plain_text',
                    'content' => 'Another Test Card',
                ],
            ],
            'elements' => [
                [
                    'tag' => 'div',
                    'text' => [
                        'tag' => 'lark_md',
                        'content' => '**Bold text** and _italic text_',
                    ],
                ],
            ],
        ]);
        $interactiveMessage2->setWebhookUrl($webhookUrl);

        $manager->persist($interactiveMessage1);
        $manager->persist($interactiveMessage2);

        $manager->flush();
    }
}
