<?php

namespace LarkCustomBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LarkCustomBotBundle\Repository\TextMessageRepository;

#[ORM\Table(name: 'fcb_text_message', options: ['comment' => '飞书文本消息'])]
#[ORM\Entity(repositoryClass: TextMessageRepository::class)]
class TextMessage extends AbstractMessage
{
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '消息内容'])]
    private string $content;

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getType(): string
    {
        return 'text';
    }

    public function toArray(): array
    {
        return [
            'msg_type' => $this->getType(),
            'content' => [
                'text' => $this->content
            ]
        ];
    }
}
