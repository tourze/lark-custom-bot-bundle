<?php

declare(strict_types=1);

namespace LarkCustomBotBundle\Service;

use LarkCustomBotBundle\Entity\AbstractMessage;
use LarkCustomBotBundle\Request\FeishuRobotRequest;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

#[WithMonologChannel(channel: 'lark_custom_bot')]
class MessageSender
{
    public function __construct(
        private readonly LarkRequestService $larkRequestService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function send(AbstractMessage $message): void
    {
        $this->logger->info('开始发送飞书消息', [
            'message_type' => $message->getType(),
            'message_id' => $message->getId(),
            'webhook_url' => $message->getWebhookUrl()->getName(),
        ]);

        try {
            $request = new FeishuRobotRequest();
            $request->setMessage($message);

            $this->larkRequestService->request($request);

            $this->logger->info('飞书消息发送成功', [
                'message_type' => $message->getType(),
                'message_id' => $message->getId(),
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('飞书消息发送失败', [
                'message_type' => $message->getType(),
                'message_id' => $message->getId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * @param AbstractMessage[] $messages
     */
    public function sendBatch(array $messages): void
    {
        if ([] === $messages) {
            return;
        }

        $this->logger->info('开始批量发送飞书消息', [
            'message_count' => count($messages),
        ]);

        $successCount = 0;
        $failureCount = 0;

        foreach ($messages as $message) {
            try {
                $this->send($message);
                ++$successCount;
            } catch (\Throwable $e) {
                ++$failureCount;
                // 继续发送其他消息，不中断批量发送流程
                $this->logger->warning('批量发送中某条消息失败，继续发送其他消息', [
                    'message_type' => $message->getType(),
                    'message_id' => $message->getId(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->logger->info('批量发送飞书消息完成', [
            'total_count' => count($messages),
            'success_count' => $successCount,
            'failure_count' => $failureCount,
        ]);

        if ($failureCount > 0) {
            throw new \RuntimeException(sprintf('批量发送部分失败：%d 成功，%d 失败，共 %d 条消息', $successCount, $failureCount, count($messages)));
        }
    }
}
