<?php

namespace LarkCustomBotBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LarkCustomBotBundle\Repository\ImageMessageRepository;

#[ORM\Table(name: 'fcb_image_message')]
#[ORM\Entity(repositoryClass: ImageMessageRepository::class)]
class ImageMessage extends AbstractMessage
{
    #[ORM\Column(length: 255, options: ['comment' => '图片URL'])]
    private string $imageKey;

    public function getImageKey(): string
    {
        return $this->imageKey;
    }

    public function setImageKey(string $imageKey): static
    {
        $this->imageKey = $imageKey;
        return $this;
    }

    public function getType(): string
    {
        return 'image';
    }

    public function toArray(): array
    {
        return [
            'msg_type' => $this->getType(),
            'content' => [
                'image_key' => $this->imageKey
            ]
        ];
    }
}
