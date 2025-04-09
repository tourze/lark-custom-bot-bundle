<?php

namespace LarkCustomBotBundle\Enum;

/**
 * @see https://open.feishu.cn/document/client-docs/bot-v3/add-custom-bot#f62e72d5
 */
enum PostTagEnum: string
{
    case TEXT = 'text';           // 纯文本
    case A = 'a';                 // 超链接
    case AT = 'at';               // @用户
    case IMG = 'img';             // 图片
    case MEDIA = 'media';         // 媒体文件
    case EMOTION = 'emotion';     // 表情
}
