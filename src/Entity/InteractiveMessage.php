<?php

namespace LarkCustomBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LarkCustomBotBundle\Repository\InteractiveMessageRepository;

#[ORM\Table(name: 'fcb_interactive_message')]
#[ORM\Entity(repositoryClass: InteractiveMessageRepository::class)]
class InteractiveMessage extends AbstractMessage
{
    #[ORM\Column(type: Types::JSON, options: ['comment' => '卡片消息内容'])]
    private array $card;

    public function getCard(): array
    {
        return $this->card;
    }

    public function setCard(array $card): static
    {
        $this->card = $card;
        return $this;
    }

    public function getType(): string
    {
        return 'interactive';
    }

    public function toArray(): array
    {
        return [
            'msg_type' => $this->getType(),
            'card' => $this->card
        ];
    }
} 