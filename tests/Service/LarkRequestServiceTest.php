<?php

namespace LarkCustomBotBundle\Tests\Service;

use HttpClientBundle\Exception\HttpClientException;
use HttpClientBundle\Request\RequestInterface;
use LarkCustomBotBundle\Service\LarkRequestService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

class LarkRequestServiceTest extends TestCase
{
    private LarkRequestService $service;

    protected function setUp(): void
    {
        $this->service = new LarkRequestService();
    }

    public function testGetRequestUrl_shouldReturnRequestPath(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $expectedPath = 'https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook';

        $request->expects($this->once())
            ->method('getRequestPath')
            ->willReturn($expectedPath);

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('getRequestUrl');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->service, [$request]);
        $this->assertEquals($expectedPath, $result);
    }

    public function testGetRequestMethod_withMethodSpecified_shouldReturnSpecifiedMethod(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $expectedMethod = 'GET';

        $request->expects($this->exactly(2))
            ->method('getRequestMethod')
            ->willReturn($expectedMethod);

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('getRequestMethod');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->service, [$request]);
        $this->assertEquals($expectedMethod, $result);
    }

    public function testGetRequestMethod_withNoMethodSpecified_shouldReturnDefaultMethod(): void
    {
        $request = $this->createMock(RequestInterface::class);

        $request->expects($this->once())
            ->method('getRequestMethod')
            ->willReturn(null);

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('getRequestMethod');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->service, [$request]);
        $this->assertEquals('POST', $result);
    }

    public function testGetRequestOptions_shouldReturnRequestOptions(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $expectedOptions = ['json' => ['key' => 'value']];

        $request->expects($this->once())
            ->method('getRequestOptions')
            ->willReturn($expectedOptions);

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('getRequestOptions');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->service, [$request]);
        $this->assertEquals($expectedOptions, $result);
    }

    public function testFormatResponse_withSuccessResponse_shouldDecodeJson(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $successJson = '{"code": 0, "data": {"key": "value"}}';
        $expectedResult = ['code' => 0, 'data' => ['key' => 'value']];

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn($successJson);

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('formatResponse');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->service, [$request, $response]);
        $this->assertEquals($expectedResult, $result);
    }

    public function testFormatResponse_withErrorResponse_shouldThrowException(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $errorJson = '{"code": 1, "msg": "Error message"}';

        $response->expects($this->atLeastOnce())
            ->method('getContent')
            ->willReturn($errorJson);

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('formatResponse');
        $method->setAccessible(true);

        $this->expectException(HttpClientException::class);
        $method->invokeArgs($this->service, [$request, $response]);
    }

    public function testFormatResponse_withInvalidJson_shouldStillDecodeJson(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        // 无效的JSON格式应该由Yiisoft\Json\Json::decode正确处理
        // 这里我们测试一个缺少code字段的响应
        $invalidJson = '{"data": {"key": "value"}}';
        $expectedResult = ['data' => ['key' => 'value']];

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn($invalidJson);

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('formatResponse');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->service, [$request, $response]);
        $this->assertEquals($expectedResult, $result);
    }
}
