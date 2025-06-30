<?php

namespace LarkCustomBotBundle\Service;

use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Exception\HttpClientException;
use HttpClientBundle\Request\RequestInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Yiisoft\Json\Json;

class LarkRequestService extends ApiClient
{
    protected function getRequestUrl(RequestInterface $request): string
    {
        return $request->getRequestPath();
    }

    protected function getRequestMethod(RequestInterface $request): string
    {
        return $request->getRequestMethod() !== null ? $request->getRequestMethod() : 'POST';
    }

    protected function getRequestOptions(RequestInterface $request): ?array
    {
        return $request->getRequestOptions();
    }

    protected function formatResponse(RequestInterface $request, ResponseInterface $response): array
    {
        $json = Json::decode($response->getContent());
        $code = $json['code'] ?? 0;
        if ($code != 0) {
            throw new HttpClientException($request, $response, $json['msg'] ?? '请求失败');
        }
        return $json;
    }
}
