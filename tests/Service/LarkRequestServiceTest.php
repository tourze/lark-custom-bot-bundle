<?php

namespace LarkCustomBotBundle\Tests\Service;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use HttpClientBundle\Exception\HttpClientException;
use HttpClientBundle\HttpClientBundle;
use HttpClientBundle\Request\RequestInterface;
use LarkCustomBotBundle\LarkCustomBotBundle;
use LarkCustomBotBundle\Service\LarkRequestService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\DoctrineAsyncInsertBundle\DoctrineAsyncInsertBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(LarkRequestService::class)]
#[RunTestsInSeparateProcesses] final class LarkRequestServiceTest extends AbstractIntegrationTestCase
{
    private LarkRequestService $service;

    /**
     * @return array<class-string, array<string, bool>>
     */
    public static function configureBundles(): array
    {
        return [
            FrameworkBundle::class => ['all' => true],
            DoctrineBundle::class => ['all' => true],
            HttpClientBundle::class => ['all' => true],
            LarkCustomBotBundle::class => ['all' => true],
            DoctrineTimestampBundle::class => ['all' => true],
            DoctrineAsyncInsertBundle::class => ['all' => true],
        ];
    }

    protected function onSetUp(): void
    {
        // 从容器中获取服务实例
        $this->service = self::getService(LarkRequestService::class);
    }

    public function testGetRequestUrlShouldReturnRequestPath(): void
    {
        $request = $this->createMock(RequestInterface::class);
        self::assertInstanceOf(RequestInterface::class, $request);
        $expectedPath = 'https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook';

        $request->expects($this->once())
            ->method('getRequestPath')
            ->willReturn($expectedPath)
        ;

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('getRequestUrl');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->service, [$request]);
        $this->assertEquals($expectedPath, $result);
    }

    public function testGetRequestMethodWithMethodSpecifiedShouldReturnSpecifiedMethod(): void
    {
        $request = $this->createMock(RequestInterface::class);
        self::assertInstanceOf(RequestInterface::class, $request);
        $expectedMethod = 'GET';

        $request->expects($this->exactly(2))
            ->method('getRequestMethod')
            ->willReturn($expectedMethod)
        ;

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('getRequestMethod');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->service, [$request]);
        $this->assertEquals($expectedMethod, $result);
    }

    public function testGetRequestMethodWithNoMethodSpecifiedShouldReturnDefaultMethod(): void
    {
        $request = $this->createMock(RequestInterface::class);
        self::assertInstanceOf(RequestInterface::class, $request);

        $request->expects($this->once())
            ->method('getRequestMethod')
            ->willReturn(null)
        ;

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('getRequestMethod');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->service, [$request]);
        $this->assertEquals('POST', $result);
    }

    public function testGetRequestOptionsShouldReturnRequestOptions(): void
    {
        $request = $this->createMock(RequestInterface::class);
        self::assertInstanceOf(RequestInterface::class, $request);
        $expectedOptions = ['json' => ['key' => 'value']];

        $request->expects($this->once())
            ->method('getRequestOptions')
            ->willReturn($expectedOptions)
        ;

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('getRequestOptions');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->service, [$request]);
        $this->assertEquals($expectedOptions, $result);
    }

    public function testFormatResponseWithSuccessResponseShouldDecodeJson(): void
    {
        $request = $this->createMock(RequestInterface::class);
        self::assertInstanceOf(RequestInterface::class, $request);
        $response = $this->createMock(ResponseInterface::class);
        self::assertInstanceOf(ResponseInterface::class, $response);

        $successJson = '{"code": 0, "data": {"key": "value"}}';
        $expectedResult = ['code' => 0, 'data' => ['key' => 'value']];

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn($successJson)
        ;

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('formatResponse');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->service, [$request, $response]);
        $this->assertEquals($expectedResult, $result);
    }

    public function testFormatResponseWithErrorResponseShouldThrowException(): void
    {
        $request = $this->createMock(RequestInterface::class);
        self::assertInstanceOf(RequestInterface::class, $request);
        $response = $this->createMock(ResponseInterface::class);
        self::assertInstanceOf(ResponseInterface::class, $response);

        $errorJson = '{"code": 1, "msg": "Error message"}';

        $response->expects($this->atLeastOnce())
            ->method('getContent')
            ->willReturn($errorJson)
        ;

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('formatResponse');
        $method->setAccessible(true);

        $this->expectException(HttpClientException::class);
        $method->invokeArgs($this->service, [$request, $response]);
    }

    public function testFormatResponseWithInvalidJsonShouldStillDecodeJson(): void
    {
        $request = $this->createMock(RequestInterface::class);
        self::assertInstanceOf(RequestInterface::class, $request);
        $response = $this->createMock(ResponseInterface::class);
        self::assertInstanceOf(ResponseInterface::class, $response);

        // 无效的JSON格式应该由Yiisoft\Json\Json::decode正确处理
        // 这里我们测试一个缺少code字段的响应
        $invalidJson = '{"data": {"key": "value"}}';
        $expectedResult = ['data' => ['key' => 'value']];

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn($invalidJson)
        ;

        $reflectionClass = new \ReflectionClass(LarkRequestService::class);
        $method = $reflectionClass->getMethod('formatResponse');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->service, [$request, $response]);
        $this->assertEquals($expectedResult, $result);
    }
}
