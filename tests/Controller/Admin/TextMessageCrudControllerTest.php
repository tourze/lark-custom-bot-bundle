<?php

declare(strict_types=1);

namespace LarkCustomBotBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use LarkCustomBotBundle\Controller\Admin\TextMessageCrudController;
use LarkCustomBotBundle\Entity\TextMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(TextMessageCrudController::class)]
#[RunTestsInSeparateProcesses]
final class TextMessageCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(TextMessage::class, TextMessageCrudController::getEntityFqcn());
    }

    #[Test]
    public function testConfigureFields(): void
    {
        $controller = new TextMessageCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);

        // 验证字段类型
        $fieldTypes = array_map(
            fn ($field) => \is_object($field) ? get_class($field) : 'unknown',
            $fields
        );

        $this->assertContains(IdField::class, $fieldTypes);
        $this->assertContains(AssociationField::class, $fieldTypes);
        $this->assertContains(TextareaField::class, $fieldTypes);
        $this->assertContains(DateTimeField::class, $fieldTypes);

        // 验证关键字段
        $fieldNames = array_map(
            fn ($field) => \is_object($field) ? $field->getAsDto()->getProperty() : '',
            $fields
        );

        $this->assertContains('id', $fieldNames);
        $this->assertContains('webhookUrl', $fieldNames);
        $this->assertContains('content', $fieldNames);
        $this->assertContains('createTime', $fieldNames);
        $this->assertContains('updateTime', $fieldNames);
    }

    #[Test]
    public function testConfigureFilters(): void
    {
        $controller = new TextMessageCrudController();
        $filters = Filters::new();
        $filtersBuilder = $controller->configureFilters($filters);

        $this->assertInstanceOf(Filters::class, $filtersBuilder);
    }

    #[Test]
    public function testIndexPageAccessible(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', '/admin/lark-bot/text-message');

        $this->assertTrue($client->getResponse()->isSuccessful(), 'Page should be accessible');
        $this->assertGreaterThan(0, $crawler->filter('body')->count(), 'Page should render successfully');
    }

    #[Test]
    public function testNewPageAccessible(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', '/admin/lark-bot/text-message/new');

        $this->assertTrue($client->getResponse()->isSuccessful(), 'New page should be accessible');
        $this->assertGreaterThan(0, $crawler->filter('body')->count(), 'Page should render successfully');
    }

    #[Test]
    public function testFormValidationWithEmptyData(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('POST', '/admin/lark-bot/text-message/new', [
            'TextMessage' => [
                'webhookUrl' => '',
                'content' => '',
            ],
        ]);

        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertTrue(
            $statusCode >= 400 || $crawler->filter('.form-error, .invalid-feedback, .alert-danger')->count() > 0,
            'Form validation should show errors for empty data'
        );
    }

    /**
     * @return TextMessageCrudController
     */
    protected function getControllerService(): TextMessageCrudController
    {
        return self::getService(TextMessageCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'webhook_url' => ['Webhook地址'];
        yield 'content' => ['消息内容'];
        yield 'create_time' => ['创建时间'];
        yield 'update_time' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'webhook_url' => ['webhookUrl'];
        yield 'content' => ['content'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'webhook_url' => ['webhookUrl'];
        yield 'content' => ['content'];
    }
}
