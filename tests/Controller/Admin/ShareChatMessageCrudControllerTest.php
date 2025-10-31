<?php

declare(strict_types=1);

namespace LarkCustomBotBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use LarkCustomBotBundle\Controller\Admin\ShareChatMessageCrudController;
use LarkCustomBotBundle\Entity\ShareChatMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ShareChatMessageCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ShareChatMessageCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(ShareChatMessage::class, ShareChatMessageCrudController::getEntityFqcn());
    }

    #[Test]
    public function testConfigureFields(): void
    {
        $controller = new ShareChatMessageCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);

        // 验证字段类型
        $fieldTypes = array_map(
            fn ($field) => \is_object($field) ? get_class($field) : 'unknown',
            $fields
        );

        $this->assertContains(IdField::class, $fieldTypes);
        $this->assertContains(AssociationField::class, $fieldTypes);
        $this->assertContains(TextField::class, $fieldTypes);
        $this->assertContains(DateTimeField::class, $fieldTypes);

        // 验证关键字段
        $fieldNames = array_map(
            fn ($field) => \is_object($field) ? $field->getAsDto()->getProperty() : '',
            $fields
        );

        $this->assertContains('id', $fieldNames);
        $this->assertContains('webhookUrl', $fieldNames);
        $this->assertContains('chatId', $fieldNames);
        $this->assertContains('createTime', $fieldNames);
        $this->assertContains('updateTime', $fieldNames);
    }

    #[Test]
    public function testConfigureFilters(): void
    {
        $controller = new ShareChatMessageCrudController();
        $filters = Filters::new();
        $filtersBuilder = $controller->configureFilters($filters);

        $this->assertInstanceOf(Filters::class, $filtersBuilder);
    }

    #[Test]
    public function testIndexPageAccessible(): void
    {
        $client = self::createClientWithDatabase();
        $this->loginAsAdmin($client);

        $crawler = $client->request('GET', '/admin/lark-bot/share-chat-message');

        $this->assertTrue($client->getResponse()->isSuccessful(), 'Page should be accessible');
        $this->assertGreaterThan(0, $crawler->filter('body')->count(), 'Page should render successfully');
    }

    #[Test]
    public function testNewPageAccessible(): void
    {
        $client = self::createClientWithDatabase();
        $this->loginAsAdmin($client);

        $crawler = $client->request('GET', '/admin/lark-bot/share-chat-message/new');

        $this->assertTrue($client->getResponse()->isSuccessful(), 'New page should be accessible');
        $this->assertGreaterThan(0, $crawler->filter('body')->count(), 'Page should render successfully');
    }

    #[Test]
    public function testFormValidationWithEmptyData(): void
    {
        $client = self::createClientWithDatabase();
        $this->loginAsAdmin($client);

        $crawler = $client->request('POST', '/admin/lark-bot/share-chat-message/new', [
            'ShareChatMessage' => [
                'webhookUrl' => '',
                'chatId' => '',
            ],
        ]);

        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertTrue(
            $statusCode >= 400 || $crawler->filter('.form-error, .invalid-feedback, .alert-danger')->count() > 0,
            'Form validation should show errors for empty data'
        );
    }

    /**
     * @return ShareChatMessageCrudController
     */
    protected function getControllerService(): ShareChatMessageCrudController
    {
        return self::getService(ShareChatMessageCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'webhook_url' => ['Webhook地址'];
        yield 'chat_id' => ['群ID'];
        yield 'create_time' => ['创建时间'];
        yield 'update_time' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'webhook_url' => ['webhookUrl'];
        yield 'chat_id' => ['chatId'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'webhook_url' => ['webhookUrl'];
        yield 'chat_id' => ['chatId'];
    }
}
