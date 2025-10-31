<?php

namespace LarkCustomBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LarkCustomBotBundle\Repository\InteractiveMessageRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'fcb_interactive_message', options: ['comment' => '飞书交互消息'])]
#[ORM\Entity(repositoryClass: InteractiveMessageRepository::class)]
class InteractiveMessage extends AbstractMessage
{
    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '卡片消息内容'])]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'array')]
    private array $card;

    /**
     * @return array<string, mixed>
     */
    public function getCard(): array
    {
        return $this->card;
    }

    /**
     * @param array<string, mixed> $card
     */
    public function setCard(array $card): void
    {
        $this->card = $card;
    }

    public function getType(): string
    {
        return 'interactive';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'msg_type' => $this->getType(),
            'card' => $this->card,
        ];
    }
}
