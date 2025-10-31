<?php

namespace LarkCustomBotBundle\Service;

use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Exception\GeneralHttpClientException;
use HttpClientBundle\Request\RequestInterface;
use HttpClientBundle\Service\SmartHttpClient;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService;
use Yiisoft\Json\Json;

#[WithMonologChannel(channel: 'lark_custom_bot')]
class LarkRequestService extends ApiClient
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SmartHttpClient $httpClient,
        private readonly LockFactory $lockFactory,
        private readonly CacheInterface $cache,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly AsyncInsertService $asyncInsertService,
    ) {
    }

    protected function getLockFactory(): LockFactory
    {
        return $this->lockFactory;
    }

    protected function getHttpClient(): SmartHttpClient
    {
        return $this->httpClient;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function getCache(): CacheInterface
    {
        return $this->cache;
    }

    protected function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    protected function getAsyncInsertService(): AsyncInsertService
    {
        return $this->asyncInsertService;
    }

    protected function getRequestUrl(RequestInterface $request): string
    {
        return $request->getRequestPath();
    }

    protected function getRequestMethod(RequestInterface $request): string
    {
        return null !== $request->getRequestMethod() ? $request->getRequestMethod() : 'POST';
    }

    protected function getRequestOptions(RequestInterface $request): ?array
    {
        return $request->getRequestOptions();
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatResponse(RequestInterface $request, ResponseInterface $response): array
    {
        $decoded = Json::decode($response->getContent());
        if (!is_array($decoded)) {
            throw new GeneralHttpClientException($request, $response, '响应格式错误：期望 JSON 对象');
        }

        /** @var array<string, mixed> $json */
        $json = $decoded;
        $code = is_int($json['code'] ?? null) ? $json['code'] : 0;
        if (0 !== $code) {
            $message = is_string($json['msg'] ?? null) ? $json['msg'] : '请求失败';
            throw new GeneralHttpClientException($request, $response, $message);
        }

        return $json;
    }
}
