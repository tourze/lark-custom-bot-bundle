<?php

namespace LarkCustomBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LarkCustomBotBundle\Repository\PostMessageRepository;
use LarkCustomBotBundle\ValueObject\PostParagraph;

#[ORM\Table(name: 'fcb_post_message', options: ['comment' => '飞书富文本消息'])]
#[ORM\Entity(repositoryClass: PostMessageRepository::class)]
class PostMessage extends AbstractMessage
{
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '标题'])]
    private string $title;

    /**
     * @var PostParagraph[]
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '富文本内容'])]
    private array $content = [];

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return PostParagraph[]
     */
    public function getContent(): array
    {
        return $this->content;
    }

    public function addParagraph(PostParagraph $paragraph): static
    {
        $this->content[] = $paragraph;
        return $this;
    }

    public function getType(): string
    {
        return 'post';
    }

    public function toArray(): array
    {
        return [
            'msg_type' => $this->getType(),
            'content' => [
                'post' => [
                    'zh_cn' => [
                        'title' => $this->title,
                        'content' => $this->content
                    ]
                ]
            ]
        ];
    }
}
