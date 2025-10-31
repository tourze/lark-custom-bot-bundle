<?php

namespace LarkCustomBotBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * @see https://open.feishu.cn/document/client-docs/bot-v3/add-custom-bot#f62e72d5
 */
enum PostTagEnum: string implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case TEXT = 'text';
    case A = 'a';
    case AT = 'at';
    case IMG = 'img';
    case MEDIA = 'media';
    case EMOTION = 'emotion';

    public function getLabel(): string
    {
        return match ($this) {
            self::TEXT => '纯文本',
            self::A => '超链接',
            self::AT => '@用户',
            self::IMG => '图片',
            self::MEDIA => '媒体文件',
            self::EMOTION => '表情',
        };
    }
}
