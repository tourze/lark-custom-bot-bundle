<?php

namespace LarkCustomBotBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LarkCustomBotBundle\Repository\ShareChatMessageRepository;

#[ORM\Table(name: 'fcb_share_chat_message')]
#[ORM\Entity(repositoryClass: ShareChatMessageRepository::class)]
class ShareChatMessage extends AbstractMessage 
{
    #[ORM\Column(length: 255, options: ['comment' => '群ID'])]
    private string $chatId;

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): static
    {
        $this->chatId = $chatId;
        return $this;
    }

    public function getType(): string
    {
        return 'share_chat';
    }

    public function toArray(): array
    {
        return [
            'msg_type' => $this->getType(),
            'content' => [
                'share_chat_id' => $this->chatId
            ]
        ];
    }
} 