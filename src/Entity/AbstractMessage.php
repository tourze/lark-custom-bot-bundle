<?php

namespace LarkCustomBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\MappedSuperclass]
abstract class AbstractMessage implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    protected int $id = 0;

    #[ORM\ManyToOne(targetEntity: WebhookUrl::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    protected WebhookUrl $webhookUrl;

    public function getId(): int
    {
        return $this->id;
    }

    public function getWebhookUrl(): WebhookUrl
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(WebhookUrl $webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function __toString(): string
    {
        return $this->getType() . '#' . $this->id;
    }

    abstract public function getType(): string;

    /**
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;
}
