<?php

declare(strict_types=1);

namespace LarkCustomBotBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use LarkCustomBotBundle\Controller\Admin\InteractiveMessageCrudController;
use LarkCustomBotBundle\Entity\InteractiveMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(InteractiveMessageCrudController::class)]
#[RunTestsInSeparateProcesses]
final class InteractiveMessageCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    
    #[Test]
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(InteractiveMessage::class, InteractiveMessageCrudController::getEntityFqcn());
    }

    #[Test]
    public function testConfigureFields(): void
    {
        $controller = new InteractiveMessageCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);

        // 验证字段类型
        $fieldTypes = array_map(
            fn ($field) => \is_object($field) ? get_class($field) : 'unknown',
            $fields
        );

        $this->assertContains(IdField::class, $fieldTypes);
        $this->assertContains(AssociationField::class, $fieldTypes);
        $this->assertContains(CodeEditorField::class, $fieldTypes);
        $this->assertContains(DateTimeField::class, $fieldTypes);

        // 验证关键字段
        $fieldNames = array_map(
            fn ($field) => \is_object($field) ? $field->getAsDto()->getProperty() : '',
            $fields
        );

        $this->assertContains('id', $fieldNames);
        $this->assertContains('webhookUrl', $fieldNames);
        $this->assertContains('card', $fieldNames);
        $this->assertContains('createTime', $fieldNames);
        $this->assertContains('updateTime', $fieldNames);
    }

    #[Test]
    public function testConfigureFilters(): void
    {
        $controller = new InteractiveMessageCrudController();
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

        $crawler = $client->request('GET', '/admin/lark-bot/interactive-message');

        $this->assertTrue($client->getResponse()->isSuccessful(), 'Page should be accessible');
        $this->assertGreaterThan(0, $crawler->filter('body')->count(), 'Page should render successfully');
    }

    #[Test]
    public function testNewPageAccessible(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', '/admin/lark-bot/interactive-message/new');

        $this->assertTrue($client->getResponse()->isSuccessful(), 'New page should be accessible');
        $this->assertGreaterThan(0, $crawler->filter('body')->count(), 'Page should render successfully');
    }

    #[Test]
    public function testFormValidationWithEmptyData(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('POST', '/admin/lark-bot/interactive-message/new', [
            'InteractiveMessage' => [
                'webhookUrl' => '',
                'card' => '',
            ],
        ]);

        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertTrue(
            $statusCode >= 400 || $crawler->filter('.form-error, .invalid-feedback, .alert-danger')->count() > 0,
            'Form validation should show errors for empty data'
        );
    }

    /**
     * @return InteractiveMessageCrudController
     */
    protected function getControllerService(): InteractiveMessageCrudController
    {
        return self::getService(InteractiveMessageCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'webhook_url' => ['Webhook地址'];
        yield 'create_time' => ['创建时间'];
        yield 'update_time' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'webhook_url' => ['webhookUrl'];
        yield 'card' => ['card'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'webhook_url' => ['webhookUrl'];
        yield 'card' => ['card'];
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
            $webhookUrl->setName('Test Webhook for Interactive Messages');
            $webhookUrl->setUrl('https://open.feishu.cn/open-apis/bot/v2/hook/test-interactive-webhook-url');
            $webhookUrl->setRemark('Test webhook for interactive messages');
            $webhookUrl->setValid(true);

            $entityManager->persist($webhookUrl);

            // 创建 InteractiveMessage 测试数据
            $interactiveMessage1 = new \LarkCustomBotBundle\Entity\InteractiveMessage();
            $interactiveMessage1->setCard([
                'config' => [
                    'wide_screen_mode' => true
                ],
                'header' => [
                    'title' => [
                        'tag' => 'plain_text',
                        'content' => 'Test Interactive Message 1'
                    ]
                ],
                'elements' => [
                    [
                        'tag' => 'div',
                        'text' => [
                            'tag' => 'plain_text',
                            'content' => 'This is a test interactive message'
                        ]
                    ]
                ]
            ]);
            $interactiveMessage1->setWebhookUrl($webhookUrl);

            $interactiveMessage2 = new \LarkCustomBotBundle\Entity\InteractiveMessage();
            $interactiveMessage2->setCard([
                'config' => [
                    'wide_screen_mode' => true
                ],
                'header' => [
                    'title' => [
                        'tag' => 'plain_text',
                        'content' => 'Test Interactive Message 2'
                    ]
                ],
                'elements' => [
                    [
                        'tag' => 'div',
                        'text' => [
                            'tag' => 'plain_text',
                            'content' => 'This is another test interactive message'
                        ]
                    ]
                ]
            ]);
            $interactiveMessage2->setWebhookUrl($webhookUrl);

            $entityManager->persist($interactiveMessage1);
            $entityManager->persist($interactiveMessage2);

            $entityManager->flush();
            self::$fixturesLoaded = true;
        } catch (\Exception $e) {
            // 如果容器或实体管理器不可用，静默失败
            // 这可能发生在某些非数据库测试中
        }
    }

    /**
     * 提取有效的动作链接
     * @return string[]
     */
    private function extractActionLinks(Crawler $crawler): array
    {
        $links = [];
        foreach ($crawler->filter('table tbody tr[data-id]') as $row) {
            $rowCrawler = new Crawler($row);
            foreach ($rowCrawler->filter('td.actions a[href]') as $a) {
                $href = $this->getSafeAttribute($a, 'href');
                if (null === $href || !$this->isValidLink($href)) {
                    continue;
                }

                if ($this->isDeleteAction($a, $href)) {
                    continue; // 删除操作需要POST与CSRF，跳过
                }

                $links[] = $href;
            }
        }

        return array_values(array_unique($links));
    }

    /**
     * 安全获取DOM属性
     */
    private function getSafeAttribute(\DOMNode $node, string $attribute): ?string
    {
        return method_exists($node, 'getAttribute') ? $node->getAttribute($attribute) : null;
    }

    /**
     * 检查链接是否有效
     */
    private function isValidLink(string $href): bool
    {
        return $href !== '' && !str_starts_with($href, 'javascript:') && $href !== '#';
    }

    /**
     * 检查是否为删除操作
     */
    private function isDeleteAction(\DOMNode $a, string $href): bool
    {
        $aCrawler = new Crawler($a);
        $actionNameAttr = strtolower($aCrawler->attr('data-action-name') ?? $aCrawler->attr('data-action') ?? '');
        $text = strtolower(trim($a->textContent ?? ''));
        $hrefLower = strtolower($href);

        return 'delete' === $actionNameAttr
            || str_contains($text, 'delete')
            || 1 === preg_match('#/delete(?:$|[/?\\#])#i', $hrefLower)
            || 1 === preg_match('/(^|[?&])crudAction=delete\b/i', $hrefLower);
    }

    /**
     * 测试链接响应状态
     */
    private function testLinkResponse(KernelBrowser $client, string $href): void
    {
        $client->request('GET', $href);

        // 跟随最多3次重定向，覆盖常见动作跳转链
        $hops = 0;
        while ($client->getResponse()->isRedirection() && $hops < 3) {
            $client->followRedirect();
            ++$hops;
        }

        $status = $client->getResponse()->getStatusCode();
        $this->assertLessThan(500, $status, sprintf('链接 %s 最终返回了 %d', $href, $status));
    }

    /**
     * 测试行操作链接不返回500错误（重写基类测试以加载测试数据）
     */
    #[Test]
    public function testIndexRowActionLinksWork(): void
    {
        $client = self::createAuthenticatedClient();

        // 加载测试数据
        $this->loadTestFixtures();

        // 访问 INDEX 页面
        $indexUrl = $this->generateAdminUrl(Action::INDEX);
        $crawler = $client->request('GET', $indexUrl);
        $this->assertTrue($client->getResponse()->isSuccessful(), 'Index page should be successful');

        // 提取有效的动作链接
        $links = $this->extractActionLinks($crawler);

        if ($links === []) {
            static::markTestSkipped('没有动作链接，跳过');
        }

        // 测试每个链接的响应
        foreach ($links as $href) {
            $this->testLinkResponse($client, $href);
        }
    }
}
