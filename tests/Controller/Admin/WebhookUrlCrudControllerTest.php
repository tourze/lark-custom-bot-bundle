<?php

declare(strict_types=1);

namespace LarkCustomBotBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use LarkCustomBotBundle\Controller\Admin\WebhookUrlCrudController;
use LarkCustomBotBundle\Entity\WebhookUrl;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(WebhookUrlCrudController::class)]
#[RunTestsInSeparateProcesses]
final class WebhookUrlCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    #[Test]
    public function testConfigureFields(): void
    {
        $controller = new WebhookUrlCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);

        // 验证字段类型
        $fieldTypes = array_map(
            fn ($field) => \is_object($field) ? get_class($field) : 'unknown',
            $fields
        );

        $this->assertContains(IdField::class, $fieldTypes);
        $this->assertContains(TextField::class, $fieldTypes);
        $this->assertContains(UrlField::class, $fieldTypes);
        $this->assertContains(BooleanField::class, $fieldTypes);
        $this->assertContains(DateTimeField::class, $fieldTypes);

        // 验证关键字段
        $fieldNames = array_map(
            fn ($field) => \is_object($field) ? $field->getAsDto()->getProperty() : '',
            $fields
        );

        $this->assertContains('id', $fieldNames);
        $this->assertContains('name', $fieldNames);
        $this->assertContains('url', $fieldNames);
        $this->assertContains('remark', $fieldNames);
        $this->assertContains('valid', $fieldNames);
        $this->assertContains('createTime', $fieldNames);
        $this->assertContains('updateTime', $fieldNames);
    }

    #[Test]
    public function testConfigureFilters(): void
    {
        $controller = new WebhookUrlCrudController();
        $filters = Filters::new();
        $filtersBuilder = $controller->configureFilters($filters);

        $this->assertInstanceOf(Filters::class, $filtersBuilder);
    }

    #[Test]
    public function testIndexPageAccessible(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', '/admin/lark-bot/webhook-url');

        $this->assertTrue($client->getResponse()->isSuccessful(), 'Page should be accessible');
        $this->assertGreaterThan(0, $crawler->filter('body')->count(), 'Page should render successfully');
    }

    #[Test]
    public function testNewPageAccessible(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', '/admin/lark-bot/webhook-url/new');

        $this->assertTrue($client->getResponse()->isSuccessful(), 'Page should be accessible');
        $this->assertGreaterThan(0, $crawler->filter('body')->count(), 'Page should render successfully');
    }

    #[Test]
    public function testFormValidationWithEmptyData(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('POST', '/admin/lark-bot/webhook-url/new', [
            'WebhookUrl' => [
                'name' => '',
                'url' => '',
            ],
        ]);

        // 直接检查响应状态和内容，不依赖静态客户端断言
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertTrue(
            $statusCode >= 400 || $crawler->filter('.form-error, .invalid-feedback, .alert-danger')->count() > 0,
            'Form validation should show errors for empty data'
        );
    }

    #[Test]
    public function testCreateWebhookUrlWithValidData(): void
    {
        $client = self::createAuthenticatedClient();

        $client->request('POST', '/admin/lark-bot/webhook-url/new', [
            'WebhookUrl' => [
                'name' => '测试Webhook',
                'url' => 'https://open.feishu.cn/open-apis/bot/v2/hook/test-webhook-url',
                'remark' => '这是一个测试用的Webhook地址',
                'valid' => true,
            ],
        ]);

        // 直接检查响应状态，不依赖静态客户端断言
        $response = $client->getResponse();
        $this->assertTrue(
            $response->isRedirection() || $response->isSuccessful(),
            sprintf('Expected successful response, got %d', $response->getStatusCode())
        );
    }

    /**
     * @return WebhookUrlCrudController
     */
    protected function getControllerService(): WebhookUrlCrudController
    {
        return self::getService(WebhookUrlCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'name' => ['名称'];
        yield 'webhook_url' => ['Webhook地址'];
        yield 'remark' => ['备注'];
        yield 'valid' => ['有效状态'];
        yield 'create_time' => ['创建时间'];
        yield 'update_time' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'url' => ['url'];
        yield 'remark' => ['remark'];
        yield 'valid' => ['valid'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'url' => ['url'];
        yield 'remark' => ['remark'];
        yield 'valid' => ['valid'];
    }
}
