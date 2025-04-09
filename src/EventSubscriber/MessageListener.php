<?php

namespace LarkCustomBotBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use LarkCustomBotBundle\Entity\AbstractMessage;
use LarkCustomBotBundle\Entity\ImageMessage;
use LarkCustomBotBundle\Entity\InteractiveMessage;
use LarkCustomBotBundle\Entity\PostMessage;
use LarkCustomBotBundle\Entity\ShareChatMessage;
use LarkCustomBotBundle\Entity\TextMessage;
use LarkCustomBotBundle\Request\FeishuRobotRequest;
use LarkCustomBotBundle\Service\LarkRequestService;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: TextMessage::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: ImageMessage::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: InteractiveMessage::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: PostMessage::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: ShareChatMessage::class)]
class MessageListener
{
    public function __construct(private readonly LarkRequestService $larkRequestService)
    {
    }

    public function postPersist(AbstractMessage $message): void
    {
        $request = new FeishuRobotRequest();
        $request->setMessage($message);
        $this->larkRequestService->request($request);
    }
}
