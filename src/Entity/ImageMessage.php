<?php

namespace LarkCustomBotBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LarkCustomBotBundle\Repository\ImageMessageRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'fcb_image_message', options: ['comment' => '飞书图片消息'])]
#[ORM\Entity(repositoryClass: ImageMessageRepository::class)]
class ImageMessage extends AbstractMessage
{
    #[ORM\Column(length: 255, options: ['comment' => '图片URL'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $imageKey;

    public function getImageKey(): string
    {
        return $this->imageKey;
    }

    public function setImageKey(string $imageKey): void
    {
        $this->imageKey = $imageKey;
    }

    public function getType(): string
    {
        return 'image';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'msg_type' => $this->getType(),
            'content' => [
                'image_key' => $this->imageKey,
            ],
        ];
    }
}
