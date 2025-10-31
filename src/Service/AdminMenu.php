<?php

declare(strict_types=1);

namespace LarkCustomBotBundle\Service;

use Knp\Menu\ItemInterface;
use LarkCustomBotBundle\Entity\ImageMessage;
use LarkCustomBotBundle\Entity\InteractiveMessage;
use LarkCustomBotBundle\Entity\PostMessage;
use LarkCustomBotBundle\Entity\ShareChatMessage;
use LarkCustomBotBundle\Entity\TextMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('飞书机器人')) {
            $item->addChild('飞书机器人');
        }

        $larkMenu = $item->getChild('飞书机器人');
        if (null === $larkMenu) {
            return;
        }

        $larkMenu
            ->addChild('Webhook地址')
            ->setUri($this->linkGenerator->getCurdListPage(WebhookUrl::class))
            ->setAttribute('icon', 'fas fa-link')
        ;

        $larkMenu
            ->addChild('文本消息')
            ->setUri($this->linkGenerator->getCurdListPage(TextMessage::class))
            ->setAttribute('icon', 'fas fa-comment')
        ;

        $larkMenu
            ->addChild('图片消息')
            ->setUri($this->linkGenerator->getCurdListPage(ImageMessage::class))
            ->setAttribute('icon', 'fas fa-image')
        ;

        $larkMenu
            ->addChild('富文本消息')
            ->setUri($this->linkGenerator->getCurdListPage(PostMessage::class))
            ->setAttribute('icon', 'fas fa-file-alt')
        ;

        $larkMenu
            ->addChild('交互消息')
            ->setUri($this->linkGenerator->getCurdListPage(InteractiveMessage::class))
            ->setAttribute('icon', 'fas fa-mouse-pointer')
        ;

        $larkMenu
            ->addChild('群分享消息')
            ->setUri($this->linkGenerator->getCurdListPage(ShareChatMessage::class))
            ->setAttribute('icon', 'fas fa-share')
        ;
    }
}
