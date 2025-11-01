<?php

declare(strict_types=1);

namespace LarkCustomBotBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use LarkCustomBotBundle\Controller\Admin\ImageMessageCrudController;
use LarkCustomBotBundle\Entity\ImageMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\DomCrawler\Crawler;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ImageMessageCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ImageMessageCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    #[Test]
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(ImageMessage::class, ImageMessageCrudController::getEntityFqcn());
    }

    #[Test]
    public function testConfigureFields(): void
    {
        $controller = new ImageMessageCrudController();
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
        $this->assertContains('imageKey', $fieldNames);
        $this->assertContains('createTime', $fieldNames);
        $this->assertContains('updateTime', $fieldNames);
    }

    #[Test]
    public function testConfigureFilters(): void
    {
        $controller = new ImageMessageCrudController();
        $filters = Filters::new();
        $filtersBuilder = $controller->configureFilters($filters);

        $this->assertInstanceOf(Filters::class, $filtersBuilder);
    }

    #[Test]
    public function testIndexPageAccessible(): void
    {
        $client = self::createAuthenticatedClient();

        // 加载测试数据以避免其他测试中的警告
        $this->loadTestFixtures();

        $crawler = $client->request('GET', '/admin/lark-bot/image-message');

        $this->assertTrue($client->getResponse()->isSuccessful(), 'Page should be accessible');
        $this->assertGreaterThan(0, $crawler->filter('body')->count(), 'Page should render successfully');
    }

    #[Test]
    public function testNewPageAccessible(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', '/admin/lark-bot/image-message/new');

        $this->assertTrue($client->getResponse()->isSuccessful(), 'New page should be accessible');
        $this->assertGreaterThan(0, $crawler->filter('body')->count(), 'Page should render successfully');
    }

    #[Test]
    public function testFormValidationWithEmptyData(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('POST', '/admin/lark-bot/image-message/new', [
            'ImageMessage' => [
                'webhookUrl' => '',
                'imageKey' => '',
            ],
        ]);

        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertTrue(
            $statusCode >= 400 || $crawler->filter('.form-error, .invalid-feedback, .alert-danger')->count() > 0,
            'Form validation should show errors for empty data'
        );
    }

    /**
     * @return ImageMessageCrudController
     */
    protected function getControllerService(): ImageMessageCrudController
    {
        return self::getService(ImageMessageCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'webhook_url' => ['Webhook地址'];
        yield 'image_key' => ['图片Key'];
        yield 'create_time' => ['创建时间'];
        yield 'update_time' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'webhook_url' => ['webhookUrl'];
        yield 'image_key' => ['imageKey'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'webhook_url' => ['webhookUrl'];
        yield 'image_key' => ['imageKey'];
    }

    
  /**
     * 创建带有测试数据的认证客户端
     */
    protected function createAuthenticatedClientWithFixtures(): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $client = self::createAuthenticatedClient();

        // 加载测试数据
        $this->loadTestFixtures();

        return $client;
    }

    /**
     * 静态数据加载标志
     */
    private static bool $fixturesLoaded = false;

    /**
     * 加载测试数据
     */
    protected function loadTestFixtures(): void
    {
        if (self::$fixturesLoaded) {
            return;
        }

        try {
            $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

            // 创建 WebhookUrl
            $webhookUrl = new \LarkCustomBotBundle\Entity\WebhookUrl();
            $webhookUrl->setName('Test Webhook for Image Messages');
            $webhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-image-webhook-url');
            $webhookUrl->setRemark('Test webhook for image messages');
            $webhookUrl->setValid(true);

            $entityManager->persist($webhookUrl);

            // 创建 ImageMessage 测试数据
            $imageMessage1 = new \LarkCustomBotBundle\Entity\ImageMessage();
            $imageMessage1->setImageKey('img_v2_test_image_key_1');
            $imageMessage1->setWebhookUrl($webhookUrl);

            $imageMessage2 = new \LarkCustomBotBundle\Entity\ImageMessage();
            $imageMessage2->setImageKey('img_v2_test_image_key_2');
            $imageMessage2->setWebhookUrl($webhookUrl);

            $entityManager->persist($imageMessage1);
            $entityManager->persist($imageMessage2);

            $entityManager->flush();
            self::$fixturesLoaded = true;
        } catch (\Exception $e) {
            // 如果容器或实体管理器不可用，静默失败
            // 这可能发生在某些非数据库测试中
        }
    }
}
