<?php

namespace LarkCustomBotBundle\Service;

use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Exception\HttpClientException;
use HttpClientBundle\Request\RequestInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Json\Json;

class FeishuRequestService extends ApiClient
{
    protected function getRequestUrl(RequestInterface $request): string
    {
        return $request->getRequestPath();
    }

    protected function getRequestMethod(RequestInterface $request): string
    {
        return $request->getRequestMethod() ?: 'POST';
    }

    protected function getRequestOptions(RequestInterface $request): ?array
    {
        return $request->getRequestOptions();
    }

    protected function formatResponse(RequestInterface $request, ResponseInterface $response): array
    {
        $json = Json::decode($response->getContent());
        $code = ArrayHelper::getValue($json, 'code');
        if ($code != 0) {
            throw new HttpClientException($request, $response, $code['msg']?? '请求失败');
        }
        return $json;
    }
}
