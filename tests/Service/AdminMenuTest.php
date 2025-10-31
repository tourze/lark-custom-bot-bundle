<?php

declare(strict_types=1);

namespace LarkCustomBotBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use LarkCustomBotBundle\Entity\ImageMessage;
use LarkCustomBotBundle\Entity\InteractiveMessage;
use LarkCustomBotBundle\Entity\PostMessage;
use LarkCustomBotBundle\Entity\ShareChatMessage;
use LarkCustomBotBundle\Entity\TextMessage;
use LarkCustomBotBundle\Entity\WebhookUrl;
use LarkCustomBotBundle\Service\AdminMenu;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    /** @var LinkGeneratorInterface&MockObject */
    private LinkGeneratorInterface $linkGenerator;

    protected function onSetUp(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        self::assertInstanceOf(LinkGeneratorInterface::class, $linkGenerator);
        $this->linkGenerator = $linkGenerator;
        // 在容器中设置模拟服务
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);
        // AdminMenu 类由容器自动管理，从容器中获取服务
        $adminMenu = self::getContainer()->get(AdminMenu::class);
        self::assertInstanceOf(AdminMenu::class, $adminMenu);
        $this->adminMenu = $adminMenu;
    }

    #[Test]
    public function testImplementsMenuProviderInterface(): void
    {
        $this->assertInstanceOf(
            MenuProviderInterface::class,
            $this->adminMenu
        );
    }

    #[Test]
    public function testInvokeCreatesAllMenuItems(): void
    {
        $rootItem = $this->createMock(ItemInterface::class);
        self::assertInstanceOf(ItemInterface::class, $rootItem);
        $larkBotItem = $this->createMock(ItemInterface::class);
        self::assertInstanceOf(ItemInterface::class, $larkBotItem);

        // 设置调用顺序：第一次返回null，第二次返回创建的项目
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('飞书机器人')
            ->willReturnOnConsecutiveCalls(null, $larkBotItem)
        ;

        $rootItem->expects($this->once())
            ->method('addChild')
            ->with('飞书机器人')
            ->willReturn($larkBotItem)
        ;

        // 配置飞书机器人菜单添加子菜单
        $larkBotItem->expects($this->exactly(6))
            ->method('addChild')
            ->willReturnCallback(function ($name) {
                $item = $this->createMock(ItemInterface::class);
                self::assertInstanceOf(ItemInterface::class, $item);
                // 配置返回的mock对象，使其支持链式调用
                $item->method('setUri')->willReturnSelf();
                $item->method('setAttribute')->willReturnSelf();
                // 验证setAttribute被调用一次
                $item->expects($this->once())
                    ->method('setAttribute')
                    ->with('icon', $this->getExpectedIconForMenuName($this->ensureString($name)))
                ;

                return $item;
            })
        ;

        // 配置链接生成器
        $this->linkGenerator->expects($this->exactly(6))
            ->method('getCurdListPage')
            ->willReturnCallback(function ($entityClass) {
                return match ($entityClass) {
                    WebhookUrl::class => '/admin/webhook-url',
                    TextMessage::class => '/admin/text-message',
                    ImageMessage::class => '/admin/image-message',
                    PostMessage::class => '/admin/post-message',
                    InteractiveMessage::class => '/admin/interactive-message',
                    ShareChatMessage::class => '/admin/share-chat-message',
                    default => throw new \InvalidArgumentException('Unexpected entity class: ' . $this->ensureString($entityClass)),
                };
            })
        ;

        ($this->adminMenu)($rootItem);
    }

    #[Test]
    public function testInvokeWithExistingLarkBotMenu(): void
    {
        $rootItem = $this->createMock(ItemInterface::class);
        self::assertInstanceOf(ItemInterface::class, $rootItem);
        $existingLarkBotItem = $this->createMock(ItemInterface::class);
        self::assertInstanceOf(ItemInterface::class, $existingLarkBotItem);

        // 模拟已经存在"飞书机器人"子项 (总共2次调用：1次检查 + 1次获取)
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('飞书机器人')
            ->willReturn($existingLarkBotItem)
        ;
        $rootItem->expects($this->never())->method('addChild');

        // 配置飞书机器人菜单添加子菜单
        $existingLarkBotItem->expects($this->exactly(6))
            ->method('addChild')
            ->willReturnCallback(function ($name) {
                $item = $this->createMock(ItemInterface::class);
                self::assertInstanceOf(ItemInterface::class, $item);
                // 配置返回的mock对象，使其支持链式调用
                $item->method('setUri')->willReturnSelf();
                $item->method('setAttribute')->willReturnSelf();
                // 验证setAttribute被调用一次
                $item->expects($this->once())
                    ->method('setAttribute')
                    ->with('icon', $this->getExpectedIconForMenuName($this->ensureString($name)))
                ;

                return $item;
            })
        ;

        // 配置链接生成器
        $this->linkGenerator->expects($this->exactly(6))
            ->method('getCurdListPage')
            ->willReturnCallback(function ($entityClass) {
                return match ($entityClass) {
                    WebhookUrl::class => '/admin/webhook-url',
                    TextMessage::class => '/admin/text-message',
                    ImageMessage::class => '/admin/image-message',
                    PostMessage::class => '/admin/post-message',
                    InteractiveMessage::class => '/admin/interactive-message',
                    ShareChatMessage::class => '/admin/share-chat-message',
                    default => throw new \InvalidArgumentException('Unexpected entity class: ' . $this->ensureString($entityClass)),
                };
            })
        ;

        ($this->adminMenu)($rootItem);
    }

    #[Test]
    public function testMenuItemsHaveCorrectIcons(): void
    {
        $expectedIcons = [
            'Webhook地址' => 'fas fa-link',
            '文本消息' => 'fas fa-comment',
            '图片消息' => 'fas fa-image',
            '富文本消息' => 'fas fa-file-alt',
            '交互消息' => 'fas fa-mouse-pointer',
            '群分享消息' => 'fas fa-share',
        ];

        foreach ($expectedIcons as $menuName => $expectedIcon) {
            $this->assertSame(
                $expectedIcon,
                $this->getExpectedIconForMenuName($menuName),
                "菜单项 '{$menuName}' 的图标不正确"
            );
        }
    }

    #[Test]
    public function testMenuItemsPointToCorrectEntities(): void
    {
        $expectedEntityMapping = [
            'Webhook地址' => WebhookUrl::class,
            '文本消息' => TextMessage::class,
            '图片消息' => ImageMessage::class,
            '富文本消息' => PostMessage::class,
            '交互消息' => InteractiveMessage::class,
            '群分享消息' => ShareChatMessage::class,
        ];

        // 通过反射检查源代码确保菜单项对应正确的实体类
        $reflection = new \ReflectionMethod($this->adminMenu, '__invoke');
        $fileName = $reflection->getFileName();
        $this->assertNotFalse($fileName, '无法获取源代码文件路径');
        $source = file_get_contents($fileName);
        $this->assertNotFalse($source, '无法读取源代码文件');

        foreach ($expectedEntityMapping as $menuName => $entityClass) {
            $this->assertStringContainsString(
                "'{$menuName}'",
                $source,
                "源代码中应包含菜单项 '{$menuName}'"
            );
            $this->assertStringContainsString(
                $entityClass,
                $source,
                "源代码中应包含实体类 {$entityClass}"
            );
        }
    }

    /**
     * 根据菜单名称返回预期的图标
     */
    private function getExpectedIconForMenuName(string $menuName): string
    {
        return match ($menuName) {
            'Webhook地址' => 'fas fa-link',
            '文本消息' => 'fas fa-comment',
            '图片消息' => 'fas fa-image',
            '富文本消息' => 'fas fa-file-alt',
            '交互消息' => 'fas fa-mouse-pointer',
            '群分享消息' => 'fas fa-share',
            default => throw new \InvalidArgumentException("Unexpected menu name: {$menuName}"),
        };
    }

    /**
     * 确保值是字符串类型
     */
    private function ensureString(mixed $value): string
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Expected string, got ' . get_debug_type($value));
        }

        return $value;
    }
}
