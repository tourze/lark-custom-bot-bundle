<?php

namespace LarkCustomBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LarkCustomBotBundle\Repository\TextMessageRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'fcb_text_message', options: ['comment' => '飞书文本消息'])]
#[ORM\Entity(repositoryClass: TextMessageRepository::class)]
class TextMessage extends AbstractMessage
{
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '消息内容'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 65535)]
    private string $content;

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getType(): string
    {
        return 'text';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'msg_type' => $this->getType(),
            'content' => [
                'text' => $this->content,
            ],
        ];
    }
}
