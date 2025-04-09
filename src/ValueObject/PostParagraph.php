<?php

namespace LarkCustomBotBundle\ValueObject;

class PostParagraph implements \JsonSerializable
{
    /**
     * @var PostNode[]
     */
    private array $nodes = [];

    public function addNode(PostNode $node): self
    {
        $this->nodes[] = $node;
        return $this;
    }

    public function addText(string $text): self
    {
        return $this->addNode(PostNode::text($text));
    }

    public function addLink(string $text, string $href): self
    {
        return $this->addNode(PostNode::link($text, $href));
    }

    public function addAt(string $userId, string $userName = ''): self
    {
        return $this->addNode(PostNode::at($userId, $userName));
    }

    public function addImage(string $imageKey): self
    {
        return $this->addNode(PostNode::image($imageKey));
    }

    public function addMedia(string $fileKey, string $imageKey): self
    {
        return $this->addNode(PostNode::media($fileKey, $imageKey));
    }

    public function addEmotion(string $emoticon): self
    {
        return $this->addNode(PostNode::emotion($emoticon));
    }

    public function jsonSerialize(): array
    {
        return $this->nodes;
    }
}
