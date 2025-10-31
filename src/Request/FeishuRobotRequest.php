<?php

namespace LarkCustomBotBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use LarkCustomBotBundle\Entity\AbstractMessage;

class FeishuRobotRequest extends ApiRequest
{
    public function getRequestPath(): string
    {
        $url = $this->getMessage()->getWebhookUrl()->getUrl();
        if (null === $url) {
            throw new \InvalidArgumentException('Webhook URL cannot be null');
        }

        return $url;
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
