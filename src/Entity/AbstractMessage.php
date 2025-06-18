<?php

namespace LarkCustomBotBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

#[ORM\MappedSuperclass]
abstract class AbstractMessage 
{
    use TimestampableAware;
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: WebhookUrl::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected WebhookUrl $webhookUrl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWebhookUrl(): WebhookUrl
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(WebhookUrl $webhookUrl): static
    {
        $this->webhookUrl = $webhookUrl;
        return $this;
    }abstract public function getType(): string;
    abstract public function toArray(): array;
}
