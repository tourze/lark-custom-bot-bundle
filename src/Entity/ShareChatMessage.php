<?php

namespace LarkCustomBotBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LarkCustomBotBundle\Repository\ShareChatMessageRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'fcb_share_chat_message', options: ['comment' => '飞书群分享消息'])]
#[ORM\Entity(repositoryClass: ShareChatMessageRepository::class)]
class ShareChatMessage extends AbstractMessage
{
    #[ORM\Column(length: 255, options: ['comment' => '群ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $chatId;

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): void
    {
        $this->chatId = $chatId;
    }

    public function getType(): string
    {
        return 'share_chat';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'msg_type' => $this->getType(),
            'content' => [
                'share_chat_id' => $this->chatId,
            ],
        ];
    }
}
