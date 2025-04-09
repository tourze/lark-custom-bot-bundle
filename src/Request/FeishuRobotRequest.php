<?php

namespace LarkCustomBotBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use LarkCustomBotBundle\Entity\AbstractMessage;

class FeishuRobotRequest extends ApiRequest
{
    public function getRequestPath(): string
    {
        return $this->getMessage()->getWebhookUrl()->getUrl();
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => $this->getMessage()->toArray(),
        ];
    }

    /**
     * 消息对象
     */
    private AbstractMessage $message;

    public function getMessage(): AbstractMessage
    {
        return $this->message;
    }

    public function setMessage(AbstractMessage $message): void
    {
        $this->message = $message;
    }
}
