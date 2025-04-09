<?php

namespace LarkCustomBotBundle\ValueObject;

use LarkCustomBotBundle\Enum\PostTagEnum;

class PostNode implements \JsonSerializable
{
    private function __construct(
        private readonly PostTagEnum $tag,
        private readonly array $attributes = []
    ) {
    }

    public static function text(string $text): self
    {
        return new self(PostTagEnum::TEXT, ['text' => $text]);
    }

    public static function link(string $text, string $href): self
    {
        return new self(PostTagEnum::A, [
            'text' => $text,
            'href' => $href
        ]);
    }

    public static function at(string $userId, string $userName = ''): self
    {
        return new self(PostTagEnum::AT, [
            'user_id' => $userId,
            'user_name' => $userName
        ]);
    }

    public static function image(string $imageKey): self
    {
        return new self(PostTagEnum::IMG, [
            'image_key' => $imageKey
        ]);
    }

    public static function media(string $fileKey, string $imageKey): self
    {
        return new self(PostTagEnum::MEDIA, [
            'file_key' => $fileKey,
            'image_key' => $imageKey
        ]);
    }

    public static function emotion(string $emoticon): self
    {
        return new self(PostTagEnum::EMOTION, [
            'emoticon' => $emoticon
        ]);
    }

    public function jsonSerialize(): array
    {
        return [
            'tag' => $this->tag->value,
            ...$this->attributes
        ];
    }
}
